<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="description" content="Apply page for a job recruitment website" />
  <meta name="keywords" content="job application, careers, HTML5, CSS3, accessibility" />
  <meta name="author" content="Lingyu Fu" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Apply | PixelCraft</title>

  <link rel="stylesheet" href="styles/apply.css" />
</head>



<body>
  <?php include 'header.inc'; ?>
  

  <main class="container">
    <section class="page-banner">
      <h2>Job Application Form</h2>
      <p>
        Thank you for your interest in joining PixelCraft. Please complete the form below carefully.
        All application records will be processed securely on the server side.
      </p>
    </section>

    <section aria-labelledby="application-form-heading" class="form-section">
      <h3 id="application-form-heading">Apply for Your Chosen Role</h3>

      <p class="required-note">
        Fields marked with <strong>*</strong> are required.
      </p>

      <?php
        if (isset($_GET['error'])) {
          echo "<div class='error-box'><p><strong>Submission failed.</strong> Please check your input and try again.</p></div>";
        }

        if (isset($_GET['success']) && isset($_GET['eoi'])) {
          $safe_eoi = htmlspecialchars($_GET['eoi']);
          echo "<div class='success-box'><p><strong>Application submitted successfully.</strong> Your EOI Number is <strong>$safe_eoi</strong>.</p></div>";
        }
      ?>

      <form method="post" action="process_eoi.php" novalidate>
        <fieldset>
          <legend>Position Information</legend>
          <div class="form-grid">
            <div class="form-group">
              <label for="jobref">Job Reference Number *</label>
              <input
                type="text"
                id="jobref"
                name="jobref"
                maxlength="5"
                placeholder="e.g. PC123"
              />
              <p class="hint">Must be exactly 5 alphanumeric characters.</p>
            </div>
          </div>
        </fieldset>

        <fieldset>
          <legend>Personal Details</legend>
          <div class="form-grid">
            <div class="form-group">
              <label for="firstname">First Name *</label>
              <input
                type="text"
                id="firstname"
                name="firstname"
                maxlength="20"
                placeholder="Enter your first name"
              />
              <p class="hint">Maximum 20 alphabetic characters.</p>
            </div>

            <div class="form-group">
              <label for="lastname">Last Name *</label>
              <input
                type="text"
                id="lastname"
                name="lastname"
                maxlength="20"
                placeholder="Enter your last name"
              />
              <p class="hint">Maximum 20 alphabetic characters.</p>
            </div>

            <div class="form-group">
              <label for="dob">Date of Birth *</label>
              <input
                type="text"
                id="dob"
                name="dob"
                maxlength="10"
                placeholder="DD/MM/YYYY"
                inputmode="numeric"
                autocomplete="bday"
              />
              <p class="hint">Use the format DD/MM/YYYY and do not enter a future date.</p>
              <p id="dob-error" class="field-error"></p>
            </div>

            <fieldset class="form-group">
              <legend>Gender *</legend>
              <div class="option-group">
                <label for="gender-male">
                  <input type="radio" id="gender-male" name="gender" value="male" />
                  Male
                </label>

                <label for="gender-female">
                  <input type="radio" id="gender-female" name="gender" value="female" />
                  Female
                </label>

                <label for="gender-nonbinary">
                  <input type="radio" id="gender-nonbinary" name="gender" value="non-binary" />
                  Non-binary
                </label>

                <label for="gender-prefernottosay">
                  <input type="radio" id="gender-prefernottosay" name="gender" value="prefer-not-to-say" />
                  Prefer not to say
                </label>
              </div>
            </fieldset>
          </div>
        </fieldset>

        <fieldset>
          <legend>Address and Contact Details</legend>
          <div class="form-grid">
            <div class="form-group full-width">
              <label for="streetaddress">Street Address *</label>
              <input
                type="text"
                id="streetaddress"
                name="streetaddress"
                maxlength="40"
                placeholder="Enter your street address"
              />
              <p class="hint">Maximum 40 characters.</p>
            </div>

            <div class="form-group">
              <label for="suburb">Suburb/Town *</label>
              <input
                type="text"
                id="suburb"
                name="suburb"
                maxlength="40"
                placeholder="Enter your suburb or town"
              />
              <p class="hint">Maximum 40 characters.</p>
            </div>

            <div class="form-group">
              <label for="state">State/Territory *</label>
              <select id="state" name="state">
                <option value="">Please select</option>
                <option value="VIC">Victoria</option>
                <option value="NSW">New South Wales</option>
                <option value="QLD">Queensland</option>
                <option value="NT">Northern Territory</option>
                <option value="WA">Western Australia</option>
                <option value="SA">South Australia</option>
                <option value="TAS">Tasmania</option>
                <option value="ACT">Australian Capital Territory</option>
              </select>
            </div>

            <div class="form-group">
              <label for="postcode">Postcode *</label>
              <input
                type="text"
                id="postcode"
                name="postcode"
                maxlength="4"
                placeholder="e.g. 3122"
              />
              <p class="hint">Must be exactly 4 digits.</p>
            </div>

            <div class="form-group">
              <label for="email">Email Address *</label>
              <input
                type="text"
                id="email"
                name="email"
                maxlength="50"
                placeholder="name@example.com"
              />
              <p class="hint">Please enter a valid email address.</p>
            </div>

            <div class="form-group">
              <label for="phone">Phone Number *</label>
              <input
                type="text"
                id="phone"
                name="phone"
                maxlength="12"
                placeholder="8 to 12 digits"
              />
              <p class="hint">Must contain 8 to 12 digits only.</p>
            </div>
          </div>
        </fieldset>

        <fieldset>
          <legend>Skills and Experience</legend>

          <fieldset class="nested-fieldset">
            <legend>Technical Skills</legend>
            <div class="option-group">
              <label for="skill-html">
                <input type="checkbox" id="skill-html" name="skills[]" value="HTML" />
                HTML
              </label>

              <label for="skill-css">
                <input type="checkbox" id="skill-css" name="skills[]" value="CSS" />
                CSS
              </label>

              <label for="skill-accessibility">
                <input type="checkbox" id="skill-accessibility" name="skills[]" value="Accessibility Design" />
                Accessibility Design
              </label>

              <label for="skill-uiux">
                <input type="checkbox" id="skill-uiux" name="skills[]" value="UI/UX Design" />
                UI/UX Design
              </label>

              <label for="skill-content">
                <input type="checkbox" id="skill-content" name="skills[]" value="Content Development" />
                Content Development
              </label>
            </div>
            <p class="hint">Select one or more relevant skills.</p>
          </fieldset>

          <div class="form-group">
            <label for="otherskills">Other Skills</label>
            <textarea
              id="otherskills"
              name="otherskills"
              rows="6"
              cols="40"
              maxlength="300"
              placeholder="List any additional skills, software knowledge, certifications, or relevant experience"
            ></textarea>
            <p class="hint">Maximum 300 characters.</p>
          </div>
        </fieldset>

        <fieldset>
          <legend>Applicant Declaration</legend>
          <div class="form-group">
            <label for="declaration">
              <input
                type="checkbox"
                id="declaration"
                name="declaration"
                value="agreed"
              />
              I declare that the information provided in this application is accurate and complete. *
            </label>
          </div>
        </fieldset>

        <div class="button-row">
          <input type="submit" value="Submit Application" class="btn-primary" />
          <input type="reset" value="Reset Form" class="btn-secondary" />
        </div>
      </form>
    </section>

    <aside class="support-panel">
      <h3>Application Tips</h3>
      <ul>
        <li>Check your job reference number carefully before submitting.</li>
        <li>Use a valid email address so our recruitment team can contact you.</li>
        <li>Complete all required fields before submission.</li>
        <li>Review your answers before pressing Submit.</li>
      </ul>
    </aside>
  </main>



  <script>
    const dobInput = document.getElementById("dob");
    const dobError = document.getElementById("dob-error");

    function validateDOB(value) {
      const dobPattern = /^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/\d{4}$/;

      if (value.length === 0) {
        return "";
      }

      if (value.length < 10) {
        return "Please enter the full date in DD/MM/YYYY format.";
      }

      if (!dobPattern.test(value)) {
        return "Invalid date format. Please use DD/MM/YYYY.";
      }

      const parts = value.split("/");
      const day = parseInt(parts[0], 10);
      const month = parseInt(parts[1], 10) - 1;
      const year = parseInt(parts[2], 10);

      const inputDate = new Date(year, month, day);
      const today = new Date();

      today.setHours(0, 0, 0, 0);

      if (
        inputDate.getFullYear() !== year ||
        inputDate.getMonth() !== month ||
        inputDate.getDate() !== day
      ) {
        return "Please enter a valid calendar date.";
      }

      if (inputDate > today) {
        return "Date of birth cannot be later than today.";
      }

      return "";
    }

    dobInput.addEventListener("input", function (e) {
      let value = e.target.value.replace(/\D/g, "");

      if (value.length > 8) {
        value = value.slice(0, 8);
      }

      if (value.length > 4) {
        value = value.slice(0, 2) + "/" + value.slice(2, 4) + "/" + value.slice(4);
      } else if (value.length > 2) {
        value = value.slice(0, 2) + "/" + value.slice(2);
      }

      e.target.value = value;

      const errorMessage = validateDOB(value);

      if (errorMessage !== "") {
        dobError.textContent = errorMessage;
        dobInput.classList.add("input-error");
      } else {
        dobError.textContent = "";
        dobInput.classList.remove("input-error");
      }
    });

    dobInput.addEventListener("blur", function () {
      const errorMessage = validateDOB(dobInput.value);

      if (errorMessage !== "") {
        dobError.textContent = errorMessage;
        dobInput.classList.add("input-error");
      } else {
        dobError.textContent = "";
        dobInput.classList.remove("input-error");
      }
    });
  </script>

</body>
<?php include 'footer.inc'; ?>

</html>