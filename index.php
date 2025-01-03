<?php
// Use Composer autoload
require 'vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use Dompdf\Dompdf;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $name = htmlspecialchars($_POST['name']);
    $fatherName = htmlspecialchars($_POST['fatherName']);
    $motherName = htmlspecialchars($_POST['motherName']);
    $dob = htmlspecialchars($_POST['dob']);
    $address = htmlspecialchars($_POST['address']);
    $religion = htmlspecialchars($_POST['religion']);
    $gender = htmlspecialchars($_POST['gender']);
    $maritalStatus = htmlspecialchars($_POST['maritalStatus']);
    $nationality = htmlspecialchars($_POST['nationality']);
    $citizenshipNo = htmlspecialchars($_POST['citizenshipNo']);

    // Language Proficiency
    $nepali = isset($_POST['nepali']) ? implode(", ", $_POST['nepali']) : "";
    $english = isset($_POST['english']) ? implode(", ", $_POST['english']) : "";
    $hindi = isset($_POST['hindi']) ? implode(", ", $_POST['hindi']) : "";

    // Academic Qualifications (dynamic table)
    $education = [];
    if (isset($_POST['level']) && is_array($_POST['level'])) {
        for ($i = 0; $i < count($_POST['level']); $i++) {
            $education[] = [
                'level' => htmlspecialchars($_POST['level'][$i]),
                'school' => htmlspecialchars($_POST['school'][$i]),
                'passedYear' => htmlspecialchars($_POST['passedYear'][$i]),
                'grade' => htmlspecialchars($_POST['grade'][$i]),
                'board' => htmlspecialchars($_POST['board'][$i])
            ];
        }
    }

    // Experience and Skills
    $experience = htmlspecialchars($_POST['experience']);
    $skills = htmlspecialchars($_POST['skills']);

    // Generate the CV in HTML format
    $cvContent = "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Generated CV</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 20px;
                padding: 0;
            }
            .cv-container {
                width: 100%;
                max-width: 800px;
                margin: 0 auto;
                padding: 20px;
                border: 1px solid #ddd;
            }
            .section {
                margin-bottom: 20px;
            }
            .section h2 {
                border-bottom: 2px solid #000;
                padding-bottom: 5px;
                font-size: 18px;
                margin-bottom: 10px;
                text-decoration: underline;
                font-weight: bold;
            }
            .section p {
                margin: 5px 0;
                font-size: 14px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
            }
            table, th, td {
                border: 1px solid #000;
                padding: 8px;
                text-align: left;
                font-size: 14px;
            }
            ul {
                list-style-type: disc;
                margin-left: 20px;
            }
        </style>
    </head>
    <body>
        <div class='cv-container'>
            <div class='section'>
                <h2>CAREER OBJECTIVES</h2>
                <p>To seek employment with a highly growth-oriented firm where the knowledge and professional skills achieved with my qualification and experience can be utilized ingeniously for the growth and prosperity of the organization, as well as for my career development.</p>
            </div>

            <div class='section'>
                <h2>PERSONAL DETAILS</h2>
                <p><strong>Name:</strong> $name</p>
                <p><strong>Father's Name:</strong> $fatherName</p>
                <p><strong>Mother's Name:</strong> $motherName</p>
                <p><strong>Date of Birth:</strong> $dob</p>
                <p><strong>Address:</strong> $address</p>
                <p><strong>Religion:</strong> $religion</p>
                <p><strong>Gender:</strong> $gender</p>
                <p><strong>Marital Status:</strong> $maritalStatus</p>
                <p><strong>Nationality:</strong> $nationality</p>
                <p><strong>Citizenship No:</strong> $citizenshipNo</p>
            </div>

            <div class='section'>
                <h2>LANGUAGE PROFICIENCY</h2>
                <ul>
                    <li><strong>Nepali:</strong> $nepali</li>
                    <li><strong>English:</strong> $english</li>
                    <li><strong>Hindi:</strong> $hindi</li>
                </ul>
            </div>

            <div class='section'>
                <h2>ACADEMIC QUALIFICATION</h2>
                <table>
                    <tr>
                        <th>Level</th>
                        <th>School/College</th>
                        <th>Passed Year</th>
                        <th>Grade</th>
                        <th>Board</th>
                    </tr>";
    foreach ($education as $edu) {
        $cvContent .= "
                    <tr>
                        <td>{$edu['level']}</td>
                        <td>{$edu['school']}</td>
                        <td>{$edu['passedYear']}</td>
                        <td>{$edu['grade']}</td>
                        <td>{$edu['board']}</td>
                    </tr>";
    }
    $cvContent .= "
                </table>
            </div>

            <div class='section'>
                <h2>EXPERIENCE</h2>
                <ul>
                    <li>$experience</li>
                </ul>
            </div>

            <div class='section'>
                <h2>SKILLS/TRAINING</h2>
                <ul>
                    <li>$skills</li>
                </ul>
            </div>
        </div>
    </body>
    </html>
    ";

    // Debug the HTML content
    // echo $cvContent; // Uncomment this line to debug the HTML content
    // exit; // Uncomment this line to stop further execution and inspect the output

    // Generate PDF
    if (isset($_POST['download_pdf'])) {
        $dompdf = new Dompdf();
        $dompdf->loadHtml($cvContent);
        $dompdf->setPaper('A4', 'portrait');

        // Disable caching
        $dompdf->set_option('isHtml5ParserEnabled', true);
        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->set_option('isPhpEnabled', true);
        $dompdf->set_option('isFontSubsettingEnabled', true);

        $dompdf->render();
        $dompdf->stream("cv.pdf", array("Attachment" => true));
        exit;
    }

    // Generate DOCX
    if (isset($_POST['download_docx'])) {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection([
            'marginLeft' => 1440,   // 1 inch left margin
            'marginRight' => 1440,  // 1 inch right margin
            'marginTop' => 1440,    // 1 inch top margin
            'marginBottom' => 1440, // 1 inch bottom margin
        ]);

        // Add content to the DOCX file
        $section->addTitle('CURRICULUM VITAE', 1);
        $section->addTextBreak(1);

        // Career Objectives
        $section->addTitle('CAREER OBJECTIVES', 2);
        $section->addText('To seek employment with a highly growth-oriented firm where the knowledge and professional skills achieved with my qualification and experience can be utilized ingeniously for the growth and prosperity of the organization, as well as for my career development.');
        $section->addTextBreak(1);

        // Personal Details
        $section->addTitle('PERSONAL DETAILS', 2);
        $section->addText("Name: $name");
        $section->addText("Father's Name: $fatherName");
        $section->addText("Mother's Name: $motherName");
        $section->addText("Date of Birth: $dob");
        $section->addText("Address: $address");
        $section->addText("Religion: $religion");
        $section->addText("Gender: $gender");
        $section->addText("Marital Status: $maritalStatus");
        $section->addText("Nationality: $nationality");
        $section->addText("Citizenship No: $citizenshipNo");
        $section->addTextBreak(1);

        // Language Proficiency
        $section->addTitle('LANGUAGE PROFICIENCY', 2);
        $section->addText("Nepali: $nepali");
        $section->addText("English: $english");
        $section->addText("Hindi: $hindi");
        $section->addTextBreak(1);

        // Academic Qualification
        $section->addTitle('ACADEMIC QUALIFICATION', 2);
        $table = $section->addTable();
        $table->addRow();
        $table->addCell(1500)->addText('Level');
        $table->addCell(3000)->addText('School/College');
        $table->addCell(1500)->addText('Passed Year');
        $table->addCell(1000)->addText('Grade');
        $table->addCell(1500)->addText('Board');
        foreach ($education as $edu) {
            $table->addRow();
            $table->addCell(1500)->addText($edu['level']);
            $table->addCell(3000)->addText($edu['school']);
            $table->addCell(1500)->addText($edu['passedYear']);
            $table->addCell(1000)->addText($edu['grade']);
            $table->addCell(1500)->addText($edu['board']);
        }
        $section->addTextBreak(1);

        // Experience
        $section->addTitle('EXPERIENCE', 2);
        $section->addText($experience);
        $section->addTextBreak(1);

        // Skills/Training
        $section->addTitle('SKILLS/TRAINING', 2);
        $section->addText($skills);

        // Save the DOCX file
        $filename = "cv.docx";
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save("php://output");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV Generator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 50%;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        h2 {
            color: #555;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }
        label {
            display: block;
            margin: 15px 0 5px;
            color: #333;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .language-options {
            display: flex;
            gap: 10px;
        }
        .education-table {
            width: 100%;
            margin-top: 10px;
        }
        .education-table input {
            width: 95%;
        }
        .add-education {
            margin-top: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        .add-education:hover {
            background-color: #0056b3;
        }
        .download-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .download-buttons button {
            background-color: #007bff;
        }
        .download-buttons button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>CV Generator</h1>
        <form id="cvForm" method="POST" onsubmit="return validateForm()">
            <h2>Personal Details</h2>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="fatherName">Father's Name:</label>
            <input type="text" id="fatherName" name="fatherName" required>

            <label for="motherName">Mother's Name:</label>
            <input type="text" id="motherName" name="motherName" required>

            <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="dob" required>

            <label for="address">Address:</label>
            <input type="text" id="address" name="address" required>

            <label for="religion">Religion:</label>
            <input type="text" id="religion" name="religion" required>

            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="Female">Female</option>
                <option value="Male">Male</option>
                <option value="Other">Other</option>
            </select>

            <label for="maritalStatus">Marital Status:</label>
            <select id="maritalStatus" name="maritalStatus" required>
                <option value="Unmarried">Unmarried</option>
                <option value="Married">Married</option>
            </select>

            <label for="nationality">Nationality:</label>
            <input type="text" id="nationality" name="nationality" required>

            <label for="citizenshipNo">Citizenship No:</label>
            <input type="text" id="citizenshipNo" name="citizenshipNo" required>

            <h2>Language Proficiency</h2>
            <label>Nepali:</label>
            <div class="language-options">
                <label><input type="checkbox" name="nepali[]" value="Speak"> Speak</label>
                <label><input type="checkbox" name="nepali[]" value="Read"> Read</label>
                <label><input type="checkbox" name="nepali[]" value="Write"> Write</label>
            </div>

            <label>English:</label>
            <div class="language-options">
                <label><input type="checkbox" name="english[]" value="Speak"> Speak</label>
                <label><input type="checkbox" name="english[]" value="Read"> Read</label>
                <label><input type="checkbox" name="english[]" value="Write"> Write</label>
            </div>

            <label>Hindi:</label>
            <div class="language-options">
                <label><input type="checkbox" name="hindi[]" value="Speak"> Speak</label>
                <label><input type="checkbox" name="hindi[]" value="Read"> Read</label>
                <label><input type="checkbox" name="hindi[]" value="Write"> Write</label>
            </div>

            <h2>Academic Qualification</h2>
            <div id="educationTable">
                <table class="education-table">
                    <tr>
                        <th>Level</th>
                        <th>School/College</th>
                        <th>Passed Year</th>
                        <th>Grade</th>
                        <th>Board</th>
                    </tr>
                    <tr>
                        <td><input type="text" name="level[]" required></td>
                        <td><input type="text" name="school[]" required></td>
                        <td><input type="text" name="passedYear[]" required></td>
                        <td><input type="text" name="grade[]" required></td>
                        <td><input type="text" name="board[]" required></td>
                    </tr>
                </table>
            </div>
            <button type="button" class="add-education" onclick="addEducationRow()">Add More</button>

            <h2>Experience</h2>
            <label for="experience">Experience:</label>
            <textarea id="experience" name="experience" rows="4" placeholder="e.g., Worked as a Waiter for 1 year."></textarea>

            <h2>Skills/Training</h2>
            <label for="skills">Skills:</label>
            <textarea id="skills" name="skills" rows="4" placeholder="e.g., Basic Computer Course, Good communication skill"></textarea>

            <button type="submit">Generate CV</button>
            <div class="download-buttons">
                <button type="submit" name="download_pdf">Download as PDF</button>
                <button type="submit" name="download_docx">Download as DOCX</button>
            </div>
        </form>
    </div>

    <script>
        // Function to add a new row to the education table
        function addEducationRow() {
            const table = document.querySelector(".education-table");
            const newRow = table.insertRow(-1);
            newRow.innerHTML = `
                <td><input type="text" name="level[]" required></td>
                <td><input type="text" name="school[]" required></td>
                <td><input type="text" name="passedYear[]" required></td>
                <td><input type="text" name="grade[]" required></td>
                <td><input type="text" name="board[]" required></td>
            `;
        }

        // Function to validate the form
        function validateForm() {
            const dob = document.getElementById('dob').value;
            const citizenshipNo = document.getElementById('citizenshipNo').value;

            // Validate Date of Birth (YYYY-MM-DD format)
            if (!/^\d{4}-\d{2}-\d{2}$/.test(dob)) {
                alert("Invalid Date of Birth format. Use YYYY-MM-DD.");
                return false;
            }

            // Validate Citizenship No (must be numeric)
            if (!/^\d+$/.test(citizenshipNo)) {
                alert("Citizenship No must be numeric.");
                return false;
            }

            return true;
        }
    </script>
</body>
</html>