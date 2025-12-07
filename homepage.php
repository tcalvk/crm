<?php 
session_start();

if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
    exit();
}

require 'model/database.php';
require 'model/users_db.php';

$user_id = $_SESSION["userId"];
$user_db = new UsersDB;
$user = $user_db->get_user_info($user_id);
$db = Database::getDB();

// Date windows for income chart and metric.
$range_options = [
    '7d'   => ['label' => 'last 7 days',   'type' => 'day',   'length' => 7],
    '30d'  => ['label' => 'last 30 days',  'type' => 'day',   'length' => 30],
    '90d'  => ['label' => 'last 90 days',  'type' => 'day',   'length' => 90],
    '6m'   => ['label' => 'last 6 months', 'type' => 'month', 'length' => 6],
    '12m'  => ['label' => 'last 12 months','type' => 'month', 'length' => 12],
    '36m'  => ['label' => 'last 36 months','type' => 'month', 'length' => 36],
];

$selected_range_key = filter_input(INPUT_GET, 'range');
if (!isset($range_options[$selected_range_key])) {
    $selected_range_key = '30d';
}
$range_meta = $range_options[$selected_range_key];

$today = new DateTime();
$income_query = '';
$start_date = null;

if ($range_meta['type'] === 'day') {
    $start_date = (clone $today)->modify('-' . ($range_meta['length'] - 1) . ' days');
} else { // month window transformed to day-based start
    $start_date = new DateTime('first day of this month');
    $start_date->modify('-' . ($range_meta['length'] - 1) . ' months');
}

// Align start to the Sunday of that week for weekly buckets.
if ((int) $start_date->format('w') !== 0) { // 0 = Sunday
    $start_date->modify('last sunday');
}
$start_week = clone $start_date;

// End week is the Sunday of the current week (may be today if today is Sunday).
$end_week = clone $today;
if ((int) $end_week->format('w') !== 0) {
    $end_week->modify('last sunday');
}

$income_query = '
    select 
        date_sub(date(ls.PaidDate), interval (dayofweek(ls.PaidDate) - 1) day) as BucketDate,
        sum(coalesce(ls.PaymentAmount, ls.TotalAmt)) as TotalPaid
    from LogStatements ls
    join Contract c on ls.ContractId = c.ContractId
    join Customer cu on c.CustomerId = cu.CustomerId
    where cu.userId = :userId
      and ls.PaidDate is not null
      and ls.PaidDate between :startDate and :endDate
      and (ls.WrittenOff is null or ls.WrittenOff = 0)
      and (c.TestContract is null or c.TestContract = 0)
    group by BucketDate
    order by BucketDate
';

$income_stmt = $db->prepare($income_query);
$income_stmt->bindValue(':userId', $user_id, PDO::PARAM_INT);
$income_stmt->bindValue(':startDate', $start_week->format('Y-m-d'));
$income_stmt->bindValue(':endDate', $today->format('Y-m-d'));
$income_stmt->execute();
$income_rows = $income_stmt->fetchAll();

$income_by_bucket = [];
foreach ($income_rows as $row) {
    $income_by_bucket[$row['BucketDate']] = (float) $row['TotalPaid'];
}

$labels = [];
$income_data = [];
$income_total = 0.0;

for ($week = clone $start_week; $week <= $end_week; $week->modify('+7 days')) {
    $bucket_key = $week->format('Y-m-d');
    $labels[] = $week->format('M j');
    $amount = $income_by_bucket[$bucket_key] ?? 0;
    $income_data[] = round($amount, 2);
    $income_total += $amount;
}

// Unpaid and overdue statements by customer.
$unpaid_stmt = $db->prepare('
    select 
        cu.CustomerId,
        cu.Name as CustomerName,
        coalesce(sum(case when ls.PaidDate is null and (ls.WrittenOff is null or ls.WrittenOff = 0) then ls.TotalAmt else 0 end), 0) as unpaid_total,
        coalesce(sum(case when ls.PaidDate is null and ls.DueDate < curdate() and (ls.WrittenOff is null or ls.WrittenOff = 0) then ls.TotalAmt else 0 end), 0) as overdue_total,
        coalesce(sum(case when ls.PaidDate is null and (ls.WrittenOff is null or ls.WrittenOff = 0) then 1 else 0 end), 0) as unpaid_count,
        coalesce(sum(case when ls.PaidDate is null and ls.DueDate < curdate() and (ls.WrittenOff is null or ls.WrittenOff = 0) then 1 else 0 end), 0) as overdue_count
    from Customer cu
    left join Contract c on cu.CustomerId = c.CustomerId
    left join LogStatements ls on c.ContractId = ls.ContractId
    where cu.userId = :userId
      and (c.TestContract is null or c.TestContract = 0)
    group by cu.CustomerId, cu.Name
    having unpaid_total > 0 or overdue_total > 0
    order by overdue_total desc, unpaid_total desc, cu.Name asc
');
$unpaid_stmt->bindValue(':userId', $user_id, PDO::PARAM_INT);
$unpaid_stmt->execute();
$unpaid_by_customer = $unpaid_stmt->fetchAll();

$open_balance = 0.0;
$overdue_balance = 0.0;
foreach ($unpaid_by_customer as $row) {
    $open_balance += (float) $row['unpaid_total'];
    $overdue_balance += (float) $row['overdue_total'];
}

// Active contracts = contracts with an active term today.
$active_stmt = $db->prepare('
    select count(distinct c.ContractId) as active_count
    from Contract c
    inner join Customer cu on c.CustomerId = cu.CustomerId
    where cu.userId = :userId
      and (c.TestContract is null or c.TestContract = 0)
      and c.Deleted is null
      and exists (
        select 1 from ContractTerm ct
        where ct.ContractId = c.ContractId
          and (ct.TermStartDate is null or ct.TermStartDate <= current_date())
          and (ct.TermEndDate is null or ct.TermEndDate >= current_date())
      )
');
$active_stmt->bindValue(':userId', $user_id, PDO::PARAM_INT);
$active_stmt->execute();
$active_contracts = $active_stmt->fetch();
$active_contract_count = (int) ($active_contracts['active_count'] ?? 0);

// Customer count for quick reference.
$customer_count_stmt = $db->prepare('select count(*) as customer_count from Customer where userId = :userId');
$customer_count_stmt->bindValue(':userId', $user_id, PDO::PARAM_INT);
$customer_count_stmt->execute();
$customer_count_row = $customer_count_stmt->fetch();
$customer_count = (int) ($customer_count_row['customer_count'] ?? 0);

// Upcoming statements (next 14 days).
$upcoming_stmt = $db->prepare('
    select 
        ls.StatementNumber,
        cu.Name as CustomerName,
        c.Name as ContractName,
        cast(ls.DueDate as date) as DueDate,
        ls.TotalAmt
    from LogStatements ls
    join Contract c on ls.ContractId = c.ContractId
    join Customer cu on c.CustomerId = cu.CustomerId
    where cu.userId = :userId
      and ls.PaidDate is null
      and (ls.WrittenOff is null or ls.WrittenOff = 0)
      and (c.TestContract is null or c.TestContract = 0)
      and ls.DueDate between curdate() and date_add(curdate(), interval 14 day)
    order by ls.DueDate asc
    limit 10
');
$upcoming_stmt->bindValue(':userId', $user_id, PDO::PARAM_INT);
$upcoming_stmt->execute();
$upcoming_statements = $upcoming_stmt->fetchAll();

include 'view/header.php'; 
?>

<style>
.dashboard-hero {
    background: linear-gradient(135deg, #0d6efd, #1b365c);
    color: #fff;
    border-radius: 12px;
    padding: 24px;
}
.dashboard-hero .subtle-text {
    color: rgba(255, 255, 255, 0.85);
}
.metric-card {
    border: 0;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
}
.metric-value {
    font-size: 1.6rem;
    font-weight: 700;
}
.subtle-text {
    color: #6c757d;
    font-size: 0.9rem;
}
.table-nowrap td, .table-nowrap th {
    white-space: nowrap;
}
.chart-container {
    position: relative;
    height: 260px;
    max-height: 320px;
}
</style>

<main class="py-4">
    <div class="dashboard-hero mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <div>
                <div class="text-uppercase mb-1" style="letter-spacing: 0.08em; font-size: 0.85rem;">Dashboard</div>
                <h1 class="h3 mb-1">Hi <?php echo htmlspecialchars($user['firstname'] ?? 'there'); ?>, here is your business snapshot.</h1>
                <div class="subtle-text">Updated <?php echo date('M j, Y'); ?></div>
            </div>
            <div class="text-right">
                <div class="small text-light">Customers</div>
                <div class="display-4 mb-0 font-weight-bold"><?php echo number_format($customer_count); ?></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card metric-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="subtle-text text-uppercase">Income (<?php echo $range_meta['label']; ?>)</div>
                        <span class="badge badge-success">Paid</span>
                    </div>
                    <div class="metric-value">$<?php echo number_format($income_total, 2); ?></div>
                <div class="subtle-text">From paid statements in the <?php echo $range_meta['label']; ?> (weekly, Sun-Sat)</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card metric-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="subtle-text text-uppercase">Open balance</div>
                        <span class="badge badge-warning text-dark">Unpaid</span>
                    </div>
                    <div class="metric-value">$<?php echo number_format($open_balance, 2); ?></div>
                    <div class="subtle-text"><?php echo number_format(count($unpaid_by_customer)); ?> customers with open statements</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card metric-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="subtle-text text-uppercase">Active contracts</div>
                        <span class="badge badge-info">Today</span>
                    </div>
                    <div class="metric-value"><?php echo number_format($active_contract_count); ?></div>
                    <div class="subtle-text">Contracts with a current term</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card metric-card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="mb-0">Cash in over the <?php echo $range_meta['label']; ?></h5>
                <form id="rangeForm" method="get" class="form-inline">
                    <label for="rangeSelect" class="subtle-text mb-0 mr-2">Range</label>
                    <select id="rangeSelect" name="range" class="form-control form-control-sm" onchange="document.getElementById('rangeForm').submit();">
                        <?php foreach ($range_options as $key => $meta): ?>
                            <option value="<?php echo htmlspecialchars($key); ?>" <?php if ($key === $selected_range_key) echo 'selected'; ?>>
                                <?php echo ucwords($meta['label']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
            <div class="chart-container">
                <canvas id="incomeChart"></canvas>
            </div>
            <?php if ($income_total === 0.0) : ?>
                <div class="subtle-text mt-2">No payments recorded in the last 30 days.</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7 mb-3">
            <div class="card metric-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0">Unpaid and overdue by customer</h5>
                        <span class="badge badge-danger">Overdue: $<?php echo number_format($overdue_balance, 2); ?></span>
                    </div>
                    <?php if (count($unpaid_by_customer) === 0): ?>
                        <div class="subtle-text">No open or overdue statements right now.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover table-nowrap mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Customer</th>
                                        <th class="text-right">Unpaid</th>
                                        <th class="text-right">Overdue</th>
                                        <th class="text-right">Open Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($unpaid_by_customer as $row): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['CustomerName']); ?></td>
                                            <td class="text-right">$<?php echo number_format($row['unpaid_total'], 2); ?></td>
                                            <td class="text-right text-danger">$<?php echo number_format($row['overdue_total'], 2); ?></td>
                                            <td class="text-right"><?php echo number_format($row['unpaid_count']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-5 mb-3">
            <div class="card metric-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0">Upcoming statements (14 days)</h5>
                        <span class="badge badge-primary"><?php echo count($upcoming_statements); ?></span>
                    </div>
                    <?php if (count($upcoming_statements) === 0): ?>
                        <div class="subtle-text">No statements due in the next two weeks.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-nowrap mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Due</th>
                                        <th>Customer</th>
                                        <th>Contract</th>
                                        <th class="text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($upcoming_statements as $statement): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($statement['DueDate']); ?></td>
                                            <td><?php echo htmlspecialchars($statement['CustomerName']); ?></td>
                                            <td><?php echo htmlspecialchars($statement['ContractName']); ?></td>
                                            <td class="text-right">$<?php echo number_format($statement['TotalAmt'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4"></script>
<script>
    (function() {
        var ctx = document.getElementById('incomeChart');
        if (!ctx) return;

        var labels = <?php echo json_encode($labels); ?>;
        var incomeData = <?php echo json_encode($income_data); ?>;
        var rangeLabel = <?php echo json_encode(ucwords($range_meta['label'])); ?>;
        var hasData = incomeData.some(function(value) { return value > 0; });

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Paid (' + rangeLabel + ')',
                    data: incomeData,
                    backgroundColor: hasData ? 'rgba(13, 110, 253, 0.6)' : 'rgba(108, 117, 125, 0.3)',
                    borderColor: '#0d6efd',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        },
                        gridLines: { color: 'rgba(0,0,0,0.05)' }
                    }],
                    xAxes: [{
                        gridLines: { display: false }
                    }]
                },
                legend: { display: false },
                tooltips: {
                    callbacks: {
                        label: function(item) {
                            return '$' + Number(item.yLabel).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        }
                    }
                }
            }
        });
    })();
</script>

<?php include 'view/footer.php'; ?>
