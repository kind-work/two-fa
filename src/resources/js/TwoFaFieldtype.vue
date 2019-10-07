<template>
    <div class="relative clearfix">
        <div v-if="isAlreadyActive" class="content">
          <p>Your account is already being protected by 2FA.</p>
        </div>
        
        <div v-if="!isCurrentUser && !isAlreadyActive" class="content">
          <p>A user must enable 2FA on their own account.</p>
        </div>
      
        <transition name="two-fa-fade">
          <div v-if="isDisabled && isCurrentUser && !isAlreadyActive" class="activate flex flex-col items-center justify-center absolute top-0 right-0 bottom-0 left-0 z-10">
            <button @click="setEnabling" class="btn btn-primary">Protect My Account with 2FA</button>
            
            <div class="content pt-2">
              <p>Enhance the security of my account by requiring a time based 2FA code when logging in.</p>
            </div>
          </div>
        </transition>
        
        <div v-if="isCurrentUser && !isAlreadyActive" class="activate-form">
          <img class="float-left" :src="meta.qrCode" />
          
          <div class="right-side float-left p-2">
            <div class="content">
              <p class="break-all"><strong>Key:</strong> {{ meta.key }}</p>
              <p class="break-all"><strong>URL:</strong> {{ meta.url }}</p>
            </div>
            
            <div class="pt-2">
              <label class="publish-field-label pb-2">
                Time Based 2FA Code
              </label>
              
              <div class="input-group">
                <input type="text" v-model="secret" class="input-text">
                <button @click="activate" class="btn btn-primary rounded-l-none" :disabled='isInValid'>Activate</button>
              </div>
            </div>
            
            <div class="content pt-2">
              <p>Don't have a 2FA App? Get one for <button @click="showApps = 'android'">Android</button> or <button @click="showApps = 'ios'">iOS</button>.</p>
              
              <ul v-if="showApps === 'android'">
                <li><a href="https://play.google.com/store/apps/details?id=org.shadowice.flocke.andotp" target="_blank">andOTP</a></li>
                <li><a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Google Authenticator</a></li>
                <li><a href="https://play.google.com/store/apps/details?id=com.authy.authy" target="_blank">Authy</a></li>
                <li><a href="https://play.google.com/store/apps/details?id=com.lastpass.authenticator" target="_blank">LastPass Authenticator</a></li>
                <li><a href="https://play.google.com/store/apps/details?id=com.duosecurity.duomobile" target="_blank">Duo Mobile</a></li>
                <li><a href="https://play.google.com/store/apps/details?id=com.azure.authenticator" target="_blank">Microsoft Authenticator</a></li>
              </ul>
              
              <ul v-if="showApps === 'ios'">
                <li><a href="https://apps.apple.com/us/app/authy/id494168017" target="_blank">Authy</a></li>
                <li><a href="https://apps.apple.com/us/app/lastpass-authenticator/id1079110004" target="_blank">LastPass Authenticator</a></li>
                <li><a href="https://apps.apple.com/us/app/duo-mobile/id422663827" target="_blank">Duo Mobile</a></li>
                <li><a href="https://apps.apple.com/us/app/microsoft-authenticator/id983156458" target="_blank">Microsoft Authenticator</a></li>
                <li><a href="https://apps.apple.com/us/app/google-authenticator/id388497605" target="_blank">Google Authenticator</a></li>
              </ul>
            </div>
          </div>
        </div>
    </div>
</template>

<script>
export default {
    mixins: [Fieldtype],
    data() {
        return {
            data: this.value,
            enabling: false,
            secret: '',
            showApps: null
        };
    },
    computed: {
      isAlreadyActive() {
        return this.data ? true : false;
      },
      isCurrentUser() {
        return this.meta.email === this.$store.state.publish.base.values.email;
      },
      isDisabled() {
        return !this.data && !this.enabling;
      },
      isInValid() {
        return /^\d{6}$/.test(this.secret) !== true;
      }
    },
    methods: {
      activate() {
        this.$axios.post(
          this.meta.actions.activate,
          {
            secret: this.secret,
            key: this.meta.key,
          }
        ).then(response => {
          this.$notify.success('2FA Activated');
          this.data = this.meta.key;
        }).catch(e => {
          this.$notify.error('Something went wrong');
        })
        .finally(() => {
          this.enabling = false;
          this.secret = '';
          this.showApps = null;
        });
      },
      setEnabling() {
        this.enabling = true;
      }
    }
};
</script>

<style lang="scss" scoped>
// Layout
.right-side {
  @media (min-width: 768px) {
    width: calc(100% - 200px);
  }
}
.activate {
  background-color: rgba(255,255,255,.85);
}
.activate-form {
  transition: 480ms filter ease-in-out 12ms;
}
.activate + .activate-form {
  filter: blur(.125em);
}

// For some reason this is not in Tailwind from Statamic
.break-all {
  word-break: break-all !important;
}
.top-0 {
  top: 0 !important;
}
.right-0 {
  right: 0 !important;
}
.bottom-0 {
  bottom: 0 !important;
}
.left-0 {
  left: 0 !important;
}
</style>