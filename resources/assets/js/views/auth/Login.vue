<template>
    <form id="loginForm" method="post" @submit.prevent="validateBeforeSubmit">
        <div :class="{'form-group' : true , 'has-danger': errors.has('email') }">
            <input type="email" class="form-control form-control-danger" placeholder="Enter email" name="email"
                   v-model="loginData.email" v-validate data-vv-rules="required|email">
        </div>
        <div :class="{'form-group' : true , 'has-danger': errors.has('password') }">
            <input type="password" class="form-control form-control-danger" placeholder="Enter Password" name="password"
                   v-model="loginData.password" v-validate data-vv-rules="required">
        </div>
        <div class="other-actions row">
            <div class="col-sm-6">
                <div class="checkbox">
                    <label class="c-input c-checkbox">
                        <input type="checkbox" name="remember" v-model="loginData.remember">
                        <span class="c-indicator"></span>
                        Remember Me
                    </label>
                </div>
            </div>
            <!-- <div class="col-sm-6 text-sm-right">
                 <a href="#" class="forgot-link">Forgot Password?</a>
                 </div> -->
        </div>
        <button class="btn btn-theme btn-full">Login</button>
    </form>
</template>

<script>
    import Auth from '../../services/auth'

    export default {
        data() {
            return {
                loginData: {
                    email: 'admin@reporthero.io',
                    password: 'adminpass',
                    remember: ''
                }
            }
        },
        methods: {
            validateBeforeSubmit(e){
                this.$validator.validateAll();

                if (!this.errors.any()) {
                    Auth.login(this.loginData).then(() => {
                        this.$router.push('/campaigns')
                    })
                }
            }
        },
    }
</script>