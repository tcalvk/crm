<?php
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}

include '../view/header.php';
?>

<main>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1 d-inline"><?php echo htmlspecialchars($property_info['Name']); ?></h4>
            <span class="badge badge-secondary ml-2">Property ID: <?php echo htmlspecialchars($property_info['PropertyId']); ?></span>
        </div>
        <a href=".?action=list_properties">Back to Properties</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Address 1:</strong> <?php echo htmlspecialchars($property_info['Address1']); ?></p>
                    <p><strong>Address 2:</strong> <?php echo htmlspecialchars($property_info['Address2']); ?></p>
                    <p><strong>Address 3:</strong> <?php echo htmlspecialchars($property_info['Address3']); ?></p>
                    <p><strong>City:</strong> <?php echo htmlspecialchars($property_info['City']); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>State:</strong> <?php echo htmlspecialchars($property_info['StateId']); ?><?php if (!empty($property_info['StateName'])) : ?> (<?php echo htmlspecialchars($property_info['StateName']); ?>)<?php endif; ?></p>
                    <p><strong>Zip:</strong> <?php echo htmlspecialchars($property_info['Zip']); ?></p>
                    <?php if ($user_info['superuser'] == 1) : ?>
                        <p><strong>User:</strong> <?php echo htmlspecialchars($property_info['OwnerEmail'] ?? ''); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../view/footer.php'; ?>
