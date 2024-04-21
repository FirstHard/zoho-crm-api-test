<template>
  <div class="container mt-5">
    <div class="row">
      <div class="col-12 col-md-4 offset-md-4 p-3 border rounded-1">
        <form @submit.prevent="submitForm">
          <div class="mb-3">
            <label for="dealName" class="form-label">Deal Name:</label>
            <input type="text" id="dealName" class="form-control" v-model="formData.dealName" required />
          </div>
          <div class="mb-3">
            <label for="dealStage" class="form-label">Deal Stage:</label>
            <input type="text" id="dealStage" class="form-control" v-model="formData.dealStage" required />
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
          <button type="submit" class="btn btn-primary">Submit</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "FormComponent",
  data() {
    return {
      formData: {
        dealName: "",
        dealStage: "",
        accountName: "",
        accountWebsite: "",
        accountPhone: "",
      },
    };
  },
  methods: {
    submitForm() {
      // console.log(this.formData);
    },
  },
};
</script>
