<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}

include 'header.php';

$required_fields = ['Name', 'Address1', 'City', 'StateId', 'Zip']; // adjust as needed
?>

<main class="container mt-4">
    <h4>Create <?php echo ucfirst($type); ?></h4>
    <form method="post" action="index.php">
        <input type="hidden" name="action" value="submit_create">
        <input type="hidden" name="type" value="<?php echo $type; ?>">

        <?php foreach ($fields as $label => $inputType): ?>
            <div class="form-group">
                <label for="<?php echo $label; ?>">
                    <?php echo ($label === 'StateId') ? 'State' : $label; ?>
                </label>
                <?php if ($label === 'StateId'): ?>
                    <select 
                        class="form-control" 
                        id="<?php echo $label; ?>" 
                        name="<?php echo $label; ?>" 
                        <?php echo in_array($label, $required_fields) ? 'required' : ''; ?>>
                        <option value="">Select a state</option>
                        <?php foreach ($states as $state): ?>
                            <option value="<?php echo $state['StateId']; ?>">
                                <?php echo $state['Name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <input 
                        type="<?php echo $inputType; ?>" 
                        class="form-control" 
                        id="<?php echo $label; ?>" 
                        name="<?php echo $label; ?>" 
                        <?php echo in_array($label, $required_fields) ? 'required' : 'placeholder="Optional"'; ?>
                    >
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <button type="submit" class="btn btn-primary mt-3">Create</button>
    </form>
</main>

<?php include 'footer.php'; ?>
