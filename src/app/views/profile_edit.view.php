<?php

use App\Controllers\EditProfileController;

use App\Core\Database;

use App\Models\UserRepository;



$db = new Database(require 'src/config/config.php');


$userRepo = new UserRepository($db);

$EP = new EditProfileController($db, $userRepo);

$user = $EP->getProfile();


$EP->handleProfileUpdate($user['id']);

$success = $EP->getSuccessMessage();
$error = $EP->getErrorMessage();
?>



<head>
    <title><?= htmlspecialchars($user['name'] ?? 'User') ?> | Profile</title>
    <style>
        .profile-header {
            background-color: var(--card-color);
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .cover-photo {
            height: 200px;
            position: relative;
        }

        .profile-info {
            padding: 20px;
            position: relative;
            text-align: center;
        }

        .profile-picture {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid var(--card-color);
            object-fit: cover;
            position: absolute;
            top: -60px;
            left: 50%;
            transform: translateX(-50%);
            background-color: var(--card-color);
        }

        .profile-name {
            margin-top: 70px;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .profile-bio {
            color: var(--text-secondary);
            margin-bottom: 15px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .profile-stats {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 15px 0;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-weight: 600;
            font-size: 18px;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 14px;
        }

        .profile-content {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        @media (min-width: 768px) {
            .profile-content {
                grid-template-columns: 1fr 2fr;
            }
        }

        .profile-card {
            background-color: var(--card-color);
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .profile-card h3 {
            margin-top: 0;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 15px;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(24, 119, 242, 0.2);
        }

        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            font-size: 15px;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-danger {
            background-color: #e41e3f;
            color: white;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #e7f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }

        .alert-danger {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 15px;
        }

        .image-upload-form {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <?php require 'src/Partials/nav.php'; ?>
    <!-- <? //php include "Partials/pageTitle.php"; 
            ?> -->
    <div class="container">
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <div class="profile-content">
            <div class="profile-card">
                <div class="author-image">
                    <h3>Profile Picture</h3>
                    <form method="post" enctype="multipart/form-data" class="image-upload-form">
                        <label for="image" class="btn" style="margin-top: 70px;">
                            <img src="src/uploads/<?= !empty($user['image_path']) ? htmlspecialchars($user['image_path']) : 'default.png' ?>" style="max-width: 120;" />
                        </label>
                        <input type="file" id="image" name="image" accept="image/*" style="display: none;"
                            onchange="this.form.submit()">
                    </form>

                </div>
                <h3>About</h3>
                <form method="post" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title"
                            value="<?= htmlspecialchars($user['title'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="age" class="form-label">Age</label>
                        <input type="number" class="form-control" id="age" name="age"
                            value="<?= htmlspecialchars($user['age'] ?? '') ?>" required min="1" max="120">
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                            value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="bio" class="form-label">Bio</label>
                        <input type="text" class="form-control" id="bio" name="bio"
                            value="<?= htmlspecialchars($user['bio'] ?? '') ?>">
                    </div>



                    <div class="form-group">
                        <label for="country">Country</label>

                        <select id="location" name="location" class="form-control">
                            <option value="" disabled <?= empty($user['location']) ? 'selected' : '' ?>>Select a country</option>
                            <?php
                            $countries = [
                                "Afghanistan",
                                "Åland Islands",
                                "Albania",
                                "Algeria",
                                "American Samoa",
                                "Andorra",
                                "Angola",
                                "Anguilla",
                                "Antarctica",
                                "Antigua and Barbuda",
                                "Argentina",
                                "Armenia",
                                "Aruba",
                                "Australia",
                                "Austria",
                                "Azerbaijan",
                                "Bahamas",
                                "Bahrain",
                                "Bangladesh",
                                "Barbados",
                                "Belarus",
                                "Belgium",
                                "Belize",
                                "Benin",
                                "Bermuda",
                                "Bhutan",
                                "Bolivia",
                                "Bosnia and Herzegovina",
                                "Botswana",
                                "Bouvet Island",
                                "Brazil",
                                "British Indian Ocean Territory",
                                "Brunei Darussalam",
                                "Bulgaria",
                                "Burkina Faso",
                                "Burundi",
                                "Cambodia",
                                "Cameroon",
                                "Canada",
                                "Cape Verde",
                                "Cayman Islands",
                                "Central African Republic",
                                "Chad",
                                "Chile",
                                "China",
                                "Christmas Island",
                                "Cocos (Keeling) Islands",
                                "Colombia",
                                "Comoros",
                                "Congo",
                                "Congo, The Democratic Republic of The",
                                "Cook Islands",
                                "Costa Rica",
                                "Cote D'ivoire",
                                "Croatia",
                                "Cuba",
                                "Cyprus",
                                "Czech Republic",
                                "Denmark",
                                "Djibouti",
                                "Dominica",
                                "Dominican Republic",
                                "Ecuador",
                                "Egypt",
                                "El Salvador",
                                "Equatorial Guinea",
                                "Eritrea",
                                "Estonia",
                                "Ethiopia",
                                "Falkland Islands (Malvinas)",
                                "Faroe Islands",
                                "Fiji",
                                "Finland",
                                "France",
                                "French Guiana",
                                "French Polynesia",
                                "French Southern Territories",
                                "Gabon",
                                "Gambia",
                                "Georgia",
                                "Germany",
                                "Ghana",
                                "Gibraltar",
                                "Greece",
                                "Greenland",
                                "Grenada",
                                "Guadeloupe",
                                "Guam",
                                "Guatemala",
                                "Guernsey",
                                "Guinea",
                                "Guinea-bissau",
                                "Guyana",
                                "Haiti",
                                "Heard Island and Mcdonald Islands",
                                "Holy See (Vatican City State)",
                                "Honduras",
                                "Hong Kong",
                                "Hungary",
                                "Iceland",
                                "India",
                                "Indonesia",
                                "Iran, Islamic Republic of",
                                "Iraq",
                                "Ireland",
                                "Isle of Man",
                                "Italy",
                                "Jamaica",
                                "Japan",
                                "Jersey",
                                "Jordan",
                                "Kazakhstan",
                                "Kenya",
                                "Kiribati",
                                "Korea, Democratic People's Republic of",
                                "Korea, Republic of",
                                "Kuwait",
                                "Kyrgyzstan",
                                "Lao People's Democratic Republic",
                                "Latvia",
                                "Lebanon",
                                "Lesotho",
                                "Liberia",
                                "Libyan Arab Jamahiriya",
                                "Liechtenstein",
                                "Lithuania",
                                "Luxembourg",
                                "Macao",
                                "Macedonia, The Former Yugoslav Republic of",
                                "Madagascar",
                                "Malawi",
                                "Malaysia",
                                "Maldives",
                                "Mali",
                                "Malta",
                                "Marshall Islands",
                                "Martinique",
                                "Mauritania",
                                "Mauritius",
                                "Mayotte",
                                "Mexico",
                                "Micronesia, Federated States of",
                                "Moldova, Republic of",
                                "Monaco",
                                "Mongolia",
                                "Montenegro",
                                "Montserrat",
                                "Morocco",
                                "Mozambique",
                                "Myanmar",
                                "Namibia",
                                "Nauru",
                                "Nepal",
                                "Netherlands",
                                "Netherlands Antilles",
                                "New Caledonia",
                                "New Zealand",
                                "Nicaragua",
                                "Niger",
                                "Nigeria",
                                "Niue",
                                "Norfolk Island",
                                "Northern Mariana Islands",
                                "Norway",
                                "Oman",
                                "Pakistan",
                                "Palau",
                                "Palestine",
                                "Panama",
                                "Papua New Guinea",
                                "Paraguay",
                                "Peru",
                                "Philippines",
                                "Pitcairn",
                                "Poland",
                                "Portugal",
                                "Puerto Rico",
                                "Qatar",
                                "Reunion",
                                "Romania",
                                "Russian Federation",
                                "Rwanda",
                                "Saint Helena",
                                "Saint Kitts and Nevis",
                                "Saint Lucia",
                                "Saint Pierre and Miquelon",
                                "Saint Vincent and The Grenadines",
                                "Samoa",
                                "San Marino",
                                "Sao Tome and Principe",
                                "Saudi Arabia",
                                "Senegal",
                                "Serbia",
                                "Seychelles",
                                "Sierra Leone",
                                "Singapore",
                                "Slovakia",
                                "Slovenia",
                                "Solomon Islands",
                                "Somalia",
                                "South Africa",
                                "South Georgia and The South Sandwich Islands",
                                "Spain",
                                "Sri Lanka",
                                "Sudan",
                                "Suriname",
                                "Svalbard and Jan Mayen",
                                "Swaziland",
                                "Sweden",
                                "Switzerland",
                                "Syrian Arab Republic",
                                "Taiwan",
                                "Tajikistan",
                                "Tanzania, United Republic of",
                                "Thailand",
                                "Timor-leste",
                                "Togo",
                                "Tokelau",
                                "Tonga",
                                "Trinidad and Tobago",
                                "Tunisia",
                                "Turkey",
                                "Turkmenistan",
                                "Turks and Caicos Islands",
                                "Tuvalu",
                                "Uganda",
                                "Ukraine",
                                "United Arab Emirates",
                                "United Kingdom",
                                "United States",
                                "United States Minor Outlying Islands",
                                "Uruguay",
                                "Uzbekistan",
                                "Vanuatu",
                                "Venezuela",
                                "Viet Nam",
                                "Virgin Islands, British",
                                "Virgin Islands, U.S.",
                                "Wallis and Futuna",
                                "Western Sahara",
                                "Yemen",
                                "Zambia",
                                "Zimbabwe"
                            ];

                            foreach ($countries as $country) {
                                $selected = ($user['location'] ?? '') === $country ? 'selected' : '';
                                echo "<option value=\"$country\" $selected>$country</option>";
                            }
                            ?>
                        </select>
                    </div>


                    <button type="submit" class="btn btn-primary" style="width: 100%;">Update Profile</button>
                </form>
            </div>

            <div>
                <!-- password -->
                <div class="profile-card" style="margin-bottom: 20px;">
                    <h3>Change Password</h3>
                    <form method="post" action="">

                        <div class="form-group">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        <div class="form-group">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                        </div>
                        <button type="submit" name="change_password" class="btn btn-primary" style="width: 100%;">
                            Update Password
                        </button>
                    </form>
                </div>

                <div class="profile-card">
                    <h3>Danger Zone</h3>
                    <form method="post" action="">
                        <input type="hidden" name="delete_account" value="1">
                        <button type="submit" class="btn btn-danger" style="width: 100%;"
                            onclick="return confirm('Are you sure you want to delete your account?')">
                            Delete Account
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php require 'src/Partials/footer.php'; ?>




</body>

</html>