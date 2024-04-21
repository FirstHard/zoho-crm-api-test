<template>
  <div class="container mt-5">
    <div class="row">
      <div class="col-12 col-md-4 offset-md-4 p-3 border rounded-1">
        <form @submit.prevent="submitForm" class="needs-validation" novalidate>
          <div class="mb-3">
            <label for="dealName" class="form-label">Deal Name:</label>
            <input type="text" id="dealName" class="form-control" v-model="formData.dealName" required />
          </div>
          <div class="mb-3">
            <label for="dealStage" class="form-label">Deal Stage:</label>
            <select class="form-select" id="dealStage" aria-label="Deal Stage" v-model="formData.dealStage" required>
              <option value="" disabled selected>Please select Deal Stage:</option>
              <option value="Qualification" selected>Qualification</option>
              <option value="Needs Analysis">Needs Analysis</option>
              <option value="Value Proposition">Value Proposition</option>
              <option value="Identify Decision Makers">Identify Decision Makers</option>
              <option value="Proposal/Price Quote">Proposal/Price Quote</option>
              <option value="Negotiation/Review">Negotiation/Review</option>
              <option value="Closed Won">Closed Won</option>
              <option value="Closed Lost">Closed Lost</option>
              <option value="Closed Lost to Competition">Closed Lost to Competition</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="dealClosingDate" class="form-label">Select Closing Deal Date:</label>
            <input type="date" id="dealClosingDate" class="form-control" v-model="formData.dealClosingDate" required />
          </div>
          <div class="mb-3">
            <label for="accountName" class="form-label">Account Name:</label>
            <input type="text" id="accountName" class="form-control" v-model="formData.accountName" required />
          </div>
          <div class="mb-3">
            <label for="accountWebsite" class="form-label">Account Website:</label>
            <input type="text" id="accountWebsite" class="form-control" v-model="formData.accountWebsite" required />
          </div>
          <div class="mb-3">
            <label for="accountPhone" class="form-label">Account Phone:</label>
            <input type="text" id="accountPhone" class="form-control" v-model="formData.accountPhone" required />
          </div>
          <div v-if="message" :class="[alertType, 'alert']">
            <button type="button" class="btn-close" @click="dismissAlert" aria-label="Close"></button>
            {{ message }}
          </div>
          <BootstrapAlert :message="alertMessage" :type="alertType" v-if="showAlert" @dismiss="dismissAlert" />
          <button type="submit" class="btn btn-primary">Submit</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import BootstrapAlert from "./BootstrapAlert.vue";

export default {
  name: "FormComponent",
  components: {
    BootstrapAlert,
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
    };
  },
  methods: {
    submitForm() {
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
            this.alertMessage = errorMessages.join('\n');
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
  },
};
</script>
