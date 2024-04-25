<template>
  <div class="container mt-3">
    <div class="row">
      <div class="col-12 col-lg-10 offset-lg-1 p-3 border rounded-1">
        <h1>Create Deal and Account entries</h1>
        <Form @submit="onSubmit">
          <div class="mb-3">
            <label for="dealName" class="form-label">Deal Name:*</label>
            <Field
              type="text"
              id="dealName"
              name="dealName"
              class="form-control"
              v-model="formData.dealName"
              :rules="[validateDealName,validateDealChars,validateDealMaxLengts]"
              placeholder="Please enter the name of the deal"
              required
            />
            <div class="form-text">Letters, numbers, spaces, apostrophes, and hyphens are allowed. Maximum 255 characters.</div>
            <ErrorMessage class="invalid-field" as="div" name="dealName" v-slot="{ veeMessage }">
              <span>{{ veeMessage }}</span>
            </ErrorMessage>
          </div>
          <div class="mb-3">
            <label for="dealStage" class="form-label">Deal Stage:*</label>
            <Field
              class="form-select"
              id="dealStage"
              aria-label="Deal Stage"
              v-model="formData.dealStage"
              name="dealStage"
              :rules="validateStage"
              as="select"
              required
            >
              <option value="" disabled selected>Please select Deal Stage:</option>
              <option value="Qualification">Qualification</option>
              <option value="Needs Analysis">Needs Analysis</option>
              <option value="Value Proposition">Value Proposition</option>
              <option value="Identify Decision Makers">Identify Decision Makers</option>
              <option value="Proposal/Price Quote">Proposal/Price Quote</option>
              <option value="Negotiation/Review">Negotiation/Review</option>
              <option value="Closed Won">Closed Won</option>
              <option value="Closed Lost">Closed Lost</option>
              <option value="Closed Lost to Competition">Closed Lost to Competition</option>
            </Field>
            <div class="form-text">Please select the stage of the deal.</div>
            <ErrorMessage as="div" name="dealStage" v-slot="{ veeMessage }">
              <span class="invalid-field">{{ veeMessage }}</span>
            </ErrorMessage>
          </div>
          <div class="mb-3">
            <label for="dealClosingDate" class="form-label">Select Closing Deal Date:*</label>
            <Field
              type="date"
              id="dealClosingDate"
              name="dealClosingDate"
              class="form-control"
              v-model="formData.dealClosingDate"
              :rules="validateClosingDate"
              required
            />
            <div class="form-text">Please select the closing date of the deal.</div>
            <ErrorMessage as="div" name="dealClosingDate" v-slot="{ veeMessage }">
              <span class="invalid-field">{{ veeMessage }}</span>
            </ErrorMessage>
          </div>
          <div class="mb-3">
            <label for="accountName" class="form-label">Account Name:*</label>
            <Field
              type="text"
              id="accountName"
              name="accountName"
              class="form-control"
              v-model="formData.accountName"
              :rules="[validateAccountName,validateAccountChars,validateAccountMaxLengts]"
              placeholder="Please enter the name of the account"
              maxlength="255"
              required
            />
            <div class="form-text">Letters, numbers, spaces, apostrophes, and hyphens are allowed. Maximum 255 characters.</div>
            <ErrorMessage as="div" name="accountName" v-slot="{ veeMessage }">
              <span class="invalid-field">{{ veeMessage }}</span>
            </ErrorMessage>
          </div>
          <div class="mb-3">
            <label for="accountWebsite" class="form-label">Account Website:*</label>
            <Field
              type="text"
              id="accountWebsite"
              name="accountWebsite"
              class="form-control"
              v-model="formData.accountWebsite"
              :rules="[validateUrl,validateUrlChars]"
              placeholder="Please enter the website of the account"
              required
            />
            <div class="form-text">
              Example: http://www.example.com. Must start with http://www., https://www., ftp://www., www., http://, https://, or ftp://
            </div>
            <ErrorMessage as="div" name="accountWebsite" v-slot="{ veeMessage }">
              <span class="invalid-field">{{ veeMessage }}</span>
            </ErrorMessage>
          </div>
          <div class="mb-3">
            <label for="accountPhone" class="form-label">Account Phone:*</label>
            <Field
              type="text"
              id="accountPhone"
              name="accountPhone"
              class="form-control"
              v-model="formData.accountPhone"
              :rules="[validatePhone,validatePhoneChars]"
              placeholder="Please enter the phone number of the account"
              maxlength="30"
              required
            />
            <div class="form-text">
              Allowed characters: numbers, plus sign (+), hyphens (-), parentheses (), spaces, commas (,), semicolons (;), and pound sign (#). Maximum 30
              characters.
            </div>
            <ErrorMessage as="div" name="accountPhone" v-slot="{ veeMessage }">
              <span class="invalid-field">{{ veeMessage }}</span>
            </ErrorMessage>
          </div>
          <div v-if="alertMessage" :class="[alertType, 'alert']">
            <BootstrapAlert :message="alertMessage" :type="alertType" v-if="showAlert" @dismiss="dismissAlert" />
          </div>
          <button type="submit" class="btn btn-primary">Submit</button>
        </Form>
      </div>
    </div>
  </div>
</template>

<script>
import BootstrapAlert from "./BootstrapAlert.vue";
import { Form, Field, ErrorMessage } from "vee-validate";

export default {
  name: "FormComponent",
  components: {
    BootstrapAlert,
    Form,
    Field,
    ErrorMessage,
  },
  data() {
    return {
      formData: {
        dealName: "",
        dealStage: "",
        dealClosingDate: "",
        accountName: "",
        accountWebsite: "",
        accountPhone: "",
      },
      showAlert: false,
      alertMessage: "",
      alertType: "danger",
      isSubmitting: false,
      message: "",
      veeMessage: "",
    };
  },
  methods: {
    onSubmit(values) {
      this.isSubmitting = true;
      axios
        .post("/api/leads", this.formData)
        .then((response) => {
          this.isSubmitting = false;
          this.alertType = "success";
          this.alertMessage = response.data.message;
          this.showAlert = true;
          this.clearForm();
        })
        .catch((error) => {
          this.isSubmitting = false;
          if (error.response.status === 422) {
            this.alertType = "danger";
            const errorMessages = Object.values(error.response.data.errors).flat();
            this.alertMessage = errorMessages.join("\n");
          } else {
            this.alertType = "danger";
            this.alertMessage = error.error;
            console.error(error);
          }
          this.showAlert = true;
        });
    },
    clearForm() {
      this.formData = {};
    },
    dismissAlert() {
      this.showAlert = false;
    },
    validateDealName(value) {
      if (!value || !value.trim()) {
        return "This field is required";
      }
      return true;
    },
    validateDealChars(value) {
      const regex = /^[A-Za-z0-9\s'\\-]+$/;
      if (!regex.test(value)) {
        return "This field must be a valid name";
      }
      return true;
    },
    validateDealMaxLengts(value) {
      if (value.length > 255) {
        return "This field lengts must be 255 or less characters";
      }
      return true;
    },
    validateAccountName(value) {
      if (!value || !value.trim()) {
        return "This field is required";
      }
      return true;
    },
    validateAccountChars(value) {
      const regex = /^[A-Za-z0-9\s'\\-]+$/;
      if (!regex.test(value)) {
        return "This field must be a valid name";
      }
      return true;
    },
    validateAccountMaxLengts(value) {
      if (value.length > 255) {
        return "This field lengts must be 255 or less characters";
      }
      return true;
    },
    validateStage(value) {
      if (!value || !value.trim()) {
        return "This field is required";
      }
      return true;
    },
    validateClosingDate(value) {
      if (!value || !value.trim()) {
        return "This field is required";
      }
      return true;
    },
    validateUrl(value) {
      if (!value || !value.trim()) {
        return "This field is required";
      }
      return true;
    },
    validateUrlChars(value) {
      const regex = /^(http:\/\/www\.|https:\/\/www\.|ftp:\/\/www\.|www\.|http:\/\/|https:\/\/|ftp:\/\/){1}[^\\x00-\\x19\\x22-\\x27\\x2A-\\x2C\\x2E-\\x2F\\x3A-\\x3F\\x5B-\\x5E\\x60\\x7B\\x7D-\\x7F]+(\.[^\\x00-\\x19\\x22\\x24-\\x2C\\x2E-\\x2F\\x3C\\x3E\\x40\\x5B-\\x5E\\x60\\x7B\\x7D-\\x7F]+)+([\/\?].*)*$/;
      if (!regex.test(value)) {
        return "This field must be a valid URL";
      }
      return true;
    },
    validatePhone(value) {
      if (!value || !value.trim()) {
        return "This field is required";
      }
      return true;
    },
    validatePhoneChars(value) {
      const regex = /^([+]?)(?![.-])(?:(?:[.-]?[ ]?[\da-zA-Z]+)+|(?:[ ]?(?![.-])[\da-zA-Z]+(?:[ .-]?[\da-zA-Z]+)?)+)?(?:(?:[,]+)?[;]?[\da-zA-Z]+(?:[#][\da-zA-Z]+)?)?[#;]?$/;
      if (!regex.test(value)) {
        return "This field must be a valid phone number";
      }
      return true;
    },
  },
};
</script>
