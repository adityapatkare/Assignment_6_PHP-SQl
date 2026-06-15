<?php
session_start();
include "db.php";
include "includes/header.php";

$message = "";

/* =========================
   SUBMIT FEEDBACK
========================= */

if (isset($_POST['submit_feedback'])) {

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $message_text = mysqli_real_escape_string($conn, $_POST['message']);

    if (!empty($name) && !empty($email) && !empty($message_text)) {

        $insert = $conn->query("
            INSERT INTO feedback
            (name, email, message)
            VALUES
            ('$name', '$email', '$message_text')
        ");

        if ($insert) {

            $message = "
            <div class='alert alert-success'>
                Feedback submitted successfully
            </div>";

        } else {

            $message = "
            <div class='alert alert-danger'>
                Database Error: " . $conn->error . "
            </div>";
        }

    } else {

        $message = "
        <div class='alert alert-danger'>
            All fields are required
        </div>";
    }
}
?>

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-md-7">

            <div class="card border-0 shadow-sm">

                <div class="card-body p-4">

                    <h2 class="fw-bold mb-4 text-center">
                        Contact Us
                    </h2>

                    <?php echo $message; ?>

                    <form method="POST">

                        <!-- NAME -->
                        <div class="mb-3">

                            <label class="form-label">
                                Full Name
                            </label>

                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   required>

                        </div>

                        <!-- EMAIL -->
                        <div class="mb-3">

                            <label class="form-label">
                                Email Address
                            </label>

                            <input type="email"
                                   name="email"
                                   class="form-control"
                                   required>

                        </div>

                        <!-- MESSAGE -->
                        <div class="mb-4">

                            <label class="form-label">
                                Message
                            </label>

                            <textarea name="message"
                                      rows="5"
                                      class="form-control"
                                      required></textarea>

                        </div>

                        <!-- BUTTON -->
                        <button type="submit"
                                name="submit_feedback"
                                class="btn btn-dark w-100 py-2">

                            Send Message

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<?php include "includes/footer.php"; ?>