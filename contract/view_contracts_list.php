<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}

include '../view/header.php';
?>

<main>
    <table class="table table-hover">
        <thead>
            <tr>
                <th scope="col">Contract Name</th>
                <th scope="col">Contract Type</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($contracts as $contract) : ?>
            <tr>
                <td><a href=".?action=view_contract&contract_id=<?php echo $contract['ContractId']; ?>"><?php echo $contract['Name']; ?></a></td>
                <td><?php echo $contract['ContractType']; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</main>

<?php include '../view/footer.php'; ?>