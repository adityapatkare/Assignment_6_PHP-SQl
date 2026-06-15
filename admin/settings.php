<?php
session_start();
include "../db.php";

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

include "includes/header.php";

// LOAD EXISTING SETTINGS
$settings = [];

if (file_exists("../settings.json")) {
    $settings = json_decode(file_get_contents("../settings.json"), true);
}

// DEFAULT VALUES
$site_name = $settings['site_name'] ?? "MyStore";
$theme = $settings['theme'] ?? "light";
$meta_title = $settings['meta_title'] ?? "";
$meta_desc = $settings['meta_desc'] ?? "";

// SAVE SETTINGS
if (isset($_POST['save'])) {

    $settings = [
        "site_name" => $_POST['site_name'],
        "theme" => $_POST['theme'],
        "meta_title" => $_POST['meta_title'],
        "meta_desc" => $_POST['meta_desc']
    ];

    file_put_contents("../settings.json", json_encode($settings));

    echo "<div class='alert alert-success'>Settings saved successfully!</div>";
}
?>

<h2 class="mb-4">Site Settings</h2>

<div class="row">

    <!-- SITE SETTINGS -->
    <div class="col-md-6">
        <div class="card p-4 mb-4">

            <h5 class="mb-3">General Settings</h5>

            <form method="POST">

                <label class="mb-1">Site Name</label>
                <input type="text" name="site_name"
                       value="<?php echo $site_name; ?>"
                       class="form-control mb-3">

                <!-- THEME SELECT -->
                <label class="mb-1">Theme</label>
                <select name="theme" class="form-control mb-3">
                    <option value="light" <?php if($theme=='light') echo "selected"; ?>>Light</option>
                    <option value="dark" <?php if($theme=='dark') echo "selected"; ?>>Dark</option>
                </select>

        </div>
    </div>

    <!-- SEO SETTINGS -->
    <div class="col-md-6">
        <div class="card p-4 mb-4">

            <h5 class="mb-3">SEO Settings</h5>

                <label class="mb-1">Meta Title</label>
                <input type="text" name="meta_title"
                       value="<?php echo $meta_title; ?>"
                       class="form-control mb-3">

                <label class="mb-1">Meta Description</label>
                <textarea name="meta_desc"
                          class="form-control mb-3"
                          rows="4"><?php echo $meta_desc; ?></textarea>

                <button name="save" class="btn btn-dark w-100">
                    Save Settings
                </button>

            </form>

        </div>
    </div>

</div>

<?php include "includes/footer.php"; ?>
