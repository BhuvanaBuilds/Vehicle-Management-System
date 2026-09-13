<?php

require_once 'includes/db.php';

$message = "";
$message_type = "success";

$edit_mode = false;
$edit_company = null;


/* =====================================================
   UPDATE COMPANY
===================================================== */

if (isset($_POST['update_company'])) {

    $company_id = intval($_POST['company_id']);

    $company_name = $_POST['company_name'];
    $contact_person = $_POST['contact_person'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $sql = "UPDATE companies
            SET company_name = ?,
                contact_person = ?,
                phone = ?,
                email = ?,
                address = ?
            WHERE company_id = ?";

    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param(
            "sssssi",
            $company_name,
            $contact_person,
            $phone,
            $email,
            $address,
            $company_id
        );

        if ($stmt->execute()) {

            $message = "Company updated successfully!";
            $message_type = "success";

        } else {

            $message = "Error updating company: " . $stmt->error;
            $message_type = "error";

        }

        $stmt->close();

    } else {

        $message = "Database error: " . $conn->error;
        $message_type = "error";
    }
}


/* =====================================================
   ADD COMPANY
===================================================== */

if (isset($_POST['add_company'])) {

    $company_name = $_POST['company_name'];
    $contact_person = $_POST['contact_person'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $sql = "INSERT INTO companies
            (company_name, contact_person, phone, email, address)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param(
            "sssss",
            $company_name,
            $contact_person,
            $phone,
            $email,
            $address
        );

        if ($stmt->execute()) {

            $message = "Company added successfully!";
            $message_type = "success";

        } else {

            $message = "Error adding company: " . $stmt->error;
            $message_type = "error";
        }

        $stmt->close();

    } else {

        $message = "Database error: " . $conn->error;
        $message_type = "error";
    }
}


/* =====================================================
   DELETE COMPANY
===================================================== */

if (isset($_GET['delete'])) {

    $company_id = intval($_GET['delete']);


    /* ---------------------------------------------
       CHECK QUOTATIONS
    --------------------------------------------- */

    $quotation_check = $conn->prepare(
        "SELECT COUNT(*) AS total
         FROM quotations
         WHERE company_id = ?"
    );


    if ($quotation_check) {

        $quotation_check->bind_param(
            "i",
            $company_id
        );

        $quotation_check->execute();

        $quotation_result =
            $quotation_check->get_result();

        $quotation_row =
            $quotation_result->fetch_assoc();

        $quotation_count =
            $quotation_row['total'];

        $quotation_check->close();


    } else {

        $quotation_count = 0;
    }


    /* ---------------------------------------------
       CHECK VEHICLES
    --------------------------------------------- */

    $vehicle_check = $conn->prepare(
        "SELECT COUNT(*) AS total
         FROM vehicles
         WHERE company_id = ?"
    );


    if ($vehicle_check) {

        $vehicle_check->bind_param(
            "i",
            $company_id
        );

        $vehicle_check->execute();

        $vehicle_result =
            $vehicle_check->get_result();

        $vehicle_row =
            $vehicle_result->fetch_assoc();

        $vehicle_count =
            $vehicle_row['total'];

        $vehicle_check->close();


    } else {

        $vehicle_count = 0;
    }


    /* ---------------------------------------------
       DON'T DELETE IF LINKED
    --------------------------------------------- */

    if (
        $quotation_count > 0 ||
        $vehicle_count > 0
    ) {

        $message =
            "⚠️ Cannot delete this company because it has "
            . "existing vehicles or quotations linked to it.";

        $message_type = "error";


    } else {


        /* -----------------------------------------
           SAFE DELETE
        ----------------------------------------- */

        $stmt = $conn->prepare(
            "DELETE FROM companies
             WHERE company_id = ?"
        );


        if ($stmt) {

            $stmt->bind_param(
                "i",
                $company_id
            );


            if ($stmt->execute()) {

                $message =
                    "Company deleted successfully!";

                $message_type = "success";


            } else {

                $message =
                    "❌ Unable to delete this company.";

                $message_type = "error";
            }


            $stmt->close();

        } else {

            $message =
                "Database error while deleting company.";

            $message_type = "error";
        }
    }
}


/* =====================================================
   EDIT COMPANY
===================================================== */

if (isset($_GET['edit'])) {

    $company_id = intval($_GET['edit']);

    $stmt = $conn->prepare(
        "SELECT *
         FROM companies
         WHERE company_id = ?"
    );

    if ($stmt) {

        $stmt->bind_param(
            "i",
            $company_id
        );

        $stmt->execute();

        $result =
            $stmt->get_result();


        if ($result->num_rows == 1) {

            $edit_company =
                $result->fetch_assoc();

            $edit_mode = true;
        }


        $stmt->close();
    }
}


/* =====================================================
   GET COMPANIES
===================================================== */

$companies = $conn->query("
    SELECT *
    FROM companies
    ORDER BY company_id DESC
");

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Companies - Vehicle Pro</title>


<style>

/* =====================================================
   RESET
===================================================== */

* {
    box-sizing: border-box;
}


/* =====================================================
   BODY
===================================================== */

body {

    margin: 0;

    padding: 0;

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    background: #f4f6f9;

    color: #222;
}


/* =====================================================
   SIDEBAR
===================================================== */

.sidebar {

    position: fixed;

    left: 0;

    top: 0;

    width: 250px;

    height: 100vh;

    background: #182433;

    color: white;

    padding: 25px 15px;

    overflow-y: auto;

    z-index: 1000;
}


.sidebar .logo {

    display: block;

    width: 100%;

    margin-bottom: 30px;

    text-align: center;
}


.sidebar .car-icon {

    font-size: 40px;

    margin-bottom: 8px;

    text-align: center;
}


.sidebar .logo h2 {

    margin: 0;

    color: white;

    font-size: 22px;
}


.sidebar .logo p {

    margin: 5px 0 0;

    color: #aeb8c5;

    font-size: 11px;
}


.sidebar .menu-title {

    margin: 20px 0 8px;

    padding: 0 10px;

    color: #8995a5;

    font-size: 11px;

    font-weight: bold;

    text-transform: uppercase;
}


.sidebar a {

    display: flex;

    align-items: center;

    width: 100%;

    margin: 4px 0;

    padding: 12px 15px;

    background: transparent;

    color: #dce3eb;

    text-decoration: none;

    border-radius: 8px;

    font-size: 14px;
}


.sidebar a span {

    margin-right: 10px;
}


.sidebar a:hover {

    background: #263548;

    color: white;
}


.sidebar a.active {

    background: #e63946;

    color: white;
}


/* =====================================================
   MAIN CONTENT
===================================================== */

.main-content {

    margin-left: 250px;

    width: calc(100% - 250px);

    min-height: 100vh;

    padding: 35px;
}


/* =====================================================
   PAGE HEADER
===================================================== */

.page-header {

    width: 100%;

    margin-bottom: 25px;
}


.page-header h1 {

    margin: 0;

    font-size: 30px;

    color: #222;
}


.page-header p {

    margin: 7px 0 0;

    font-size: 14px;

    color: #777;
}


/* =====================================================
   MESSAGE
===================================================== */

.message {

    width: 100%;

    margin-bottom: 20px;

    padding: 14px 16px;

    border-radius: 8px;

    font-size: 14px;

    font-weight: 500;
}


.message.success {

    background: #dff6e4;

    color: #18743c;

    border-left: 4px solid #28a745;
}


.message.error {

    background: #fde2e2;

    color: #b42318;

    border-left: 4px solid #e63946;
}


/* =====================================================
   FORM CARD
===================================================== */

.form-card {

    width: 100%;

    margin-bottom: 30px;

    padding: 28px;

    background: white;

    border-radius: 14px;

    box-shadow:
        0 4px 15px rgba(0,0,0,0.08);
}


.form-card h2 {

    margin: 0 0 25px;

    color: #2c3e50;

    font-size: 22px;
}


.form-grid {

    display: grid;

    grid-template-columns:
        1fr 1fr;

    gap: 20px;

    width: 100%;
}


.form-group {

    display: flex;

    flex-direction: column;

    width: 100%;
}


.form-group label {

    margin-bottom: 8px;

    font-size: 14px;

    font-weight: bold;

    color: #444;
}


.form-group input,
.form-group textarea {

    width: 100%;

    padding: 12px;

    border: 1px solid #d6d9dd;

    border-radius: 7px;

    font-size: 14px;

    outline: none;
}


.form-group input:focus,
.form-group textarea:focus {

    border-color: #e63946;
}


.form-group textarea {

    min-height: 80px;

    resize: vertical;
}


/* =====================================================
   BUTTONS
===================================================== */

.button-row {

    margin-top: 22px;

    display: flex;

    gap: 10px;
}


.submit-btn {

    padding: 13px 28px;

    background: #222;

    color: white;

    border: none;

    border-radius: 7px;

    font-size: 14px;

    font-weight: bold;

    cursor: pointer;
}


.submit-btn:hover {

    background: #e63946;
}


.cancel-btn {

    display: inline-block;

    padding: 13px 28px;

    background: #ddd;

    color: #333;

    text-decoration: none;

    border-radius: 7px;

    font-size: 14px;

    font-weight: bold;
}


.cancel-btn:hover {

    background: #bbb;
}


/* =====================================================
   TABLE
===================================================== */

.table-card {

    width: 100%;

    padding: 28px;

    background: white;

    border-radius: 14px;

    box-shadow:
        0 4px 15px rgba(0,0,0,0.08);

    overflow-x: auto;
}


.table-card h2 {

    margin: 0 0 20px;

    color: #2c3e50;

    font-size: 22px;
}


table {

    width: 100%;

    border-collapse: collapse;
}


th {

    padding: 14px;

    background: #222;

    color: white;

    text-align: left;

    font-size: 13px;
}


td {

    padding: 14px;

    border-bottom: 1px solid #eee;

    font-size: 13px;

    color: #444;
}


tr:hover {

    background: #f8f9fa;
}


/* =====================================================
   ACTION BUTTONS
===================================================== */

.edit-btn {

    display: inline-block;

    padding: 7px 12px;

    background: #3498db;

    color: white;

    text-decoration: none;

    border-radius: 5px;

    font-size: 12px;

    font-weight: bold;

    margin-right: 5px;
}


.edit-btn:hover {

    background: #217dbb;
}


.delete-btn {

    display: inline-block;

    padding: 7px 12px;

    background: #e63946;

    color: white;

    text-decoration: none;

    border-radius: 5px;

    font-size: 12px;

    font-weight: bold;
}


.delete-btn:hover {

    background: #c62828;
}


/* =====================================================
   MOBILE
===================================================== */

@media (max-width: 850px) {

    .sidebar {

        position: relative;

        width: 100%;

        height: auto;
    }


    .main-content {

        margin-left: 0;

        width: 100%;

        padding: 20px;
    }


    .form-grid {

        grid-template-columns: 1fr;
    }

}

</style>

</head>


<body>


<?php include 'includes/sidebar.php'; ?>


<div class="main-content">


    <!-- =================================================
         PAGE HEADER
    ================================================== -->

    <div class="page-header">

        <h1>
            🏢 Company Management
        </h1>

        <p>
            Manage customer companies and their contact information.
        </p>

    </div>



    <!-- =================================================
         MESSAGE
    ================================================== -->

    <?php if ($message != ""): ?>

        <div class="message <?php echo $message_type; ?>">

            <?php
            echo htmlspecialchars($message);
            ?>

        </div>

    <?php endif; ?>



    <!-- =================================================
         FORM
    ================================================== -->

    <div class="form-card">


        <?php if ($edit_mode): ?>

            <h2>
                ✏️ Edit Company
            </h2>

        <?php else: ?>

            <h2>
                ➕ Add New Company
            </h2>

        <?php endif; ?>


        <form method="POST">


            <?php if ($edit_mode): ?>

                <input
                    type="hidden"
                    name="company_id"
                    value="<?php
                    echo $edit_company['company_id'];
                    ?>"
                >

            <?php endif; ?>


            <div class="form-grid">


                <!-- COMPANY NAME -->

                <div class="form-group">

                    <label>
                        Company Name
                    </label>

                    <input
                        type="text"
                        name="company_name"
                        placeholder="Enter company name"
                        value="<?php

                        echo $edit_mode
                            ? htmlspecialchars(
                                $edit_company['company_name']
                              )
                            : '';

                        ?>"
                        required
                    >

                </div>



                <!-- CONTACT PERSON -->

                <div class="form-group">

                    <label>
                        Contact Person
                    </label>

                    <input
                        type="text"
                        name="contact_person"
                        placeholder="Enter contact person"
                        value="<?php

                        echo $edit_mode
                            ? htmlspecialchars(
                                $edit_company['contact_person']
                              )
                            : '';

                        ?>"
                    >

                </div>



                <!-- PHONE -->

                <div class="form-group">

                    <label>
                        Phone Number
                    </label>

                    <input
                        type="text"
                        name="phone"
                        placeholder="Enter phone number"
                        value="<?php

                        echo $edit_mode
                            ? htmlspecialchars(
                                $edit_company['phone']
                              )
                            : '';

                        ?>"
                    >

                </div>



                <!-- EMAIL -->

                <div class="form-group">

                    <label>
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        placeholder="Enter email address"
                        value="<?php

                        echo $edit_mode
                            ? htmlspecialchars(
                                $edit_company['email']
                              )
                            : '';

                        ?>"
                    >

                </div>



                <!-- ADDRESS -->

                <div
                    class="form-group"
                    style="grid-column: 1 / -1;"
                >

                    <label>
                        Address
                    </label>

                    <textarea
                        name="address"
                        placeholder="Enter company address"
                    ><?php

                    echo $edit_mode
                        ? htmlspecialchars(
                            $edit_company['address']
                          )
                        : '';

                    ?></textarea>

                </div>


            </div>



            <!-- BUTTONS -->

            <div class="button-row">


                <?php if ($edit_mode): ?>


                    <button
                        type="submit"
                        name="update_company"
                        class="submit-btn"
                    >
                        Update Company
                    </button>


                    <a
                        href="companies.php"
                        class="cancel-btn"
                    >
                        Cancel
                    </a>


                <?php else: ?>


                    <button
                        type="submit"
                        name="add_company"
                        class="submit-btn"
                    >
                        Add Company
                    </button>


                <?php endif; ?>


            </div>


        </form>

    </div>



    <!-- =================================================
         COMPANY LIST
    ================================================== -->

    <div class="table-card">


        <h2>
            📋 Company List
        </h2>


        <table>


            <thead>

                <tr>

                    <th>ID</th>

                    <th>Company Name</th>

                    <th>Contact Person</th>

                    <th>Phone</th>

                    <th>Email</th>

                    <th>Address</th>

                    <th>Action</th>

                </tr>

            </thead>


            <tbody>


            <?php if (
                $companies &&
                $companies->num_rows > 0
            ): ?>


                <?php while (
                    $company =
                    $companies->fetch_assoc()
                ): ?>


                    <tr>


                        <td>

                            <?php
                            echo $company['company_id'];
                            ?>

                        </td>


                        <td>

                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $company['company_name']
                                );

                                ?>

                            </strong>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $company['contact_person']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $company['phone']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $company['email']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $company['address']
                            );

                            ?>

                        </td>


                        <td>


                            <!-- EDIT -->

                            <a
                                href="companies.php?edit=<?php
                                echo $company['company_id'];
                                ?>"
                                class="edit-btn"
                            >
                                Edit
                            </a>


                            <!-- DELETE -->

                            <a
                                href="companies.php?delete=<?php
                                echo $company['company_id'];
                                ?>"
                                class="delete-btn"
                                onclick="
                                return confirm(
                                'Are you sure you want to delete this company?'
                                );
                                "
                            >
                                Delete
                            </a>


                        </td>


                    </tr>


                <?php endwhile; ?>


            <?php else: ?>


                <tr>

                    <td
                        colspan="7"
                        style="
                        text-align:center;
                        padding:30px;
                        "
                    >

                        No companies found.

                    </td>

                </tr>


            <?php endif; ?>


            </tbody>

        </table>

    </div>


</div>


</body>

</html>