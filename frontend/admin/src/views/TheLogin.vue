<template>
    <div class="register_page_body">
        <div class="register_page_main">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="register_page">
                            <div class="register_page_form_sec">
                                <div class="register_page_form_header">
                                    <a href="#"><img src="@/assets/images/logo.png" alt=""></a>
                                    <h3>User Login</h3>
                                </div>
                                <div class="register_page_form">
                                    <div v-if="validation.error.message" class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <strong>{{ validation.error.message }}</strong>
                                        <button @click="validation.error.message = null" type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="rp_form_single">
                                        <label for="rpfs2">Email Address</label>
                                        <input type="email" v-model="input.email" placeholder="Enter Email Address">
                                    </div>
                                    <div class="rp_form_single">
                                        <label for="rpfs3">Password</label>
                                        <input type="password" v-model="input.password" placeholder="Enter Password">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div @click="input.rememberMe = !input.rememberMe" class="register_page_checkbox">
                                                <div class="hrh2_send_notif_des">
                                                    <input type="checkbox" v-model="input.rememberMe">
                                                    <label for="html"></label>
                                                </div>
                                                <div class="rpc_lbl">
                                                    <label for="html">Remember Me</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="register_page_checkbox float-md-right float-sm-none">
                                                <div class="rpc_lbl">
                                                    <label for="html">
                                                        <a href="javascript:void(0)" @click="this.$router.push({name: 'forgot-password'})">Forgot Password?</a>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="register_page_form_btn">
                                        <input :disabled="input.email === '' || input.password === ''" v-if="!validation.loading" type="submit" @click="oauth" value="Login Now!">
                                        <button v-if="validation.loading" type="submit" disabled>
                                            <span class="spinner-border spinner-border-large" role="status" aria-hidden="true"></span>
                                            Loading...
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import axios from 'axios';
import { userAuth } from '../stores/index';
const authStore = userAuth();

export default {
    data() {
        return {
            input: {
                email: '',
                password: '',
                rememberMe: true
            },
            validation: {
                loading: false,
                error: {
                    message: null
                }
            }
        }
    },
    methods: {
        oauth() {
            let self = this;

            self.validation.loading = true;
            axios.post(`${process.env.APP_URL}/oauth/token`, {
                grant_type: process.env.OAUTH_GRANT_TYPE,
                client_id: process.env.OAUTH_CLIENT_ID,
                client_secret:process.env.OAUTH_SECRET,
                username: self.input.email,
                password: self.input.password,
                scope: process.env.OAUTH_SCOPE,
            })
            .then(function (response) {
                self.login(response);
            })
            .catch(function (error) {
                self.validation.loading = false;
                self.validation.error.message = error.response.data.message;
            });
        },
        login(data: any) {
            let self = this;
            
            axios.get(`${process.env.API_URL}/login`, {
                headers: {
                    Authorization: `Bearer ${data.data.access_token}`,
                },
            })
            .then(response => {
                authStore.setUserId(response.data.data.id);
                authStore.setAccessToken(data.data.access_token);

                if(self.input.rememberMe) {
                    localStorage.setItem("userId", response.data.data.id);
                    localStorage.setItem("accessToken", data.data.access_token);
                }

                if(response.data.data.is_admin) {
                    self.$router.push({ name: 'users' });
                } else {
                    self.validation.error.message = 'Unauthorized access.';
                    self.validation.loading = false;
                }
            })
            .catch(error => {
                self.validation.loading = false;
                self.validation.error.message = error.response.data.message;
            });
        }
    }
}
</script>