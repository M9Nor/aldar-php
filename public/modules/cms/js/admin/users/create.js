import Form from 'form-backend-validation';

const app = new Vue({
    el: '#user_add_user',
    data() {
        return {
            method  : 'GET',
            action  : '',
            avatar  : null,
            formData: {},
            form    : new Form()
        }
    },
    mounted: function() {
        this.avatar = new KTAvatar('kt_user_add_avatar');
        this.initWizard();
    },
    methods: {
        formSubmit (e) {
            var that        = this;
            var thisForm    = e.target;
            var btn         = this.$refs.submit;

            that.formData   = {};
            that.method     = thisForm.method;
            that.action     = thisForm.action;

            KTApp.progress(btn);

            $('.invalid-feedback').removeClass('is-invalid');
            $('.form-control-feedback').remove();

            // Append all form inputs to data array to send them with the request.
            $.each(thisForm.elements, function (key, input) {
                if(input.files != null && input.files.length > 0 && input.files[0] != undefined)
                {
                    Vue.set(that.formData, input.name, input.files[0]);
                }
                else
                {
                    Vue.set(that.formData, input.name, input.value);
                }
            });
            console.log(that.formData);
            // Instantiate a form class with some values
            // const thisForm = new Form(that.formData);

            that.form.withData(that.formData)

            console.log(that.form)
            
            that.form.post(that.action)
            .then(response => {
                console.log(response)
            })
            .catch(error => {
                console.log(error)
            })
            // .finally(function () {
            //     KTApp.unprogress(btn);
            // });
        },
        initWizard () {
            // Initialize form wizard
            var wizard = new KTWizard('user_add_user', {
                startStep: 1, // initial active step number
                clickableSteps: true  // allow step clicking
            });

            // Change event
            wizard.on('change', function(wizard) {
                KTUtil.scrollTop();
            });
        },
        initValidation () {
            var validator = form.validate({
                // Validate only visible fields
                // ignore: ":hidden",

                // Validation rules
                rules: {
                    // Step 1
                    profile_avatar: {
                        //required: true
                    },
                    name: {
                        required: true
                    },
                    email: {
                        required: true
                    },
                    profile_phone: {
                        required: true
                    },
                    profile_email: {
                        required: true,
                        email: true
                    }
                },

                // Display error
                invalidHandler: function(event, validator) {
                    console.log(validator)
                    KTUtil.scrollTop();

                    swal.fire({
                        "title": "",
                        "text": "There are some errors in your submission. Please correct them.",
                        "type": "error",
                        "buttonStyling": false,
                        "confirmButtonClass": "btn btn-brand btn-sm btn-bold"
                    });
                },

                // Submit valid form
                submitHandler: function (form) {

                }
            });
        },
        initSubmit () {
            var btn = form.find('[data-ktwizard-type="action-submit"]');

            btn.on('click', function(e) {
                e.preventDefault();

                if (validator.form()) {
                    // See: src\js\framework\base\app.js
                    KTApp.progress(btn);

                    // See: http://malsup.com/jquery/form/#ajaxSubmit
                    form.ajaxSubmit({
                        success: function() {
                            KTApp.unprogress(btn);

                            swal.fire({
                                "title": "",
                                "text": "The application has been successfully submitted!",
                                "type": "success",
                                "confirmButtonClass": "btn btn-secondary"
                            });
                        }
                    });
                }
            });
        }
    }
});