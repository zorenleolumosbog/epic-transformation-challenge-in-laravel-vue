<template>
    <div id="countdown">
        <div class="countdown_single">
            <div class="countdown" id="days">{{ countdown.days }}</div>
            <div class="rs_countdown_text_pos">
                <p>Days</p>
            </div>
        </div>
        <div class="countdown_single">
            <div class="countdown" id="hours">{{ countdown.hours }}</div>
            <div class="rs_countdown_text_pos">
                <p>Hours</p>
            </div>
        </div>
        <div class="countdown_single">
            <div class="countdown" id="minutes">{{ countdown.minutes }}</div>
            <div class="rs_countdown_text_pos">
                <p>Minutes</p>
            </div>
        </div>
        <div class="countdown_single">
            <div class="countdown" id="seconds">{{ countdown.seconds }}</div>
            <div class="rs_countdown_text_pos">
                <p>Seconds</p>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import axios from 'axios';

export default {
    data() {
        return {
            countdown: {
                days: 0,
                hours: 0,
                minutes: 0,
                seconds: 0
            }
        }
    },
    mounted() {
        this.getCurrentEndDateTime();  
    },
    methods: {
        getCurrentEndDateTime() {
            let self = this;
            axios.get(`${process.env.API_URL}/end-datetime`)
            .then(function (response) {
                self.getCoundownTimer(response.data.data.value);
            })
        },
        getCoundownTimer(endDate: string) {
            // Set the date and time to countdown to
            let countdownEnd = new Date(endDate).getTime();

            // Update the countdown every second
            let countdownInterval = setInterval(() => {
                // Get the start date and time
                let now = new Date().getTime();

                // Calculate the difference between the countdown date and the current date
                let difference = countdownEnd - now;

                // Calculate the days, hours, minutes, and seconds until the countdown date
                let days = Math.floor(difference / (1000 * 60 * 60 * 24));
                this.countdown.days = days;

                let hours = Math.floor(
                    (difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
                );
                this.countdown.hours = hours;
                
                let minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
                this.countdown.minutes = minutes;
                
                let seconds = Math.floor((difference % (1000 * 60)) / 1000);
                this.countdown.seconds = seconds;

                // If the countdown is over, clear the interval
                if (difference < 0) {
                    clearInterval(countdownInterval);
                    this.countdown = {
                        days: 0,
                        hours: 0,
                        minutes: 0,
                        seconds: 0
                    }

                    this.getCurrentEndDateTime();
                    this.$emit('resetPosting');
                }
            }, 1000);
        }
    }
}
</script>