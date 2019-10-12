<template>
    <div class="relative clearfix">
        <div v-if="isAlreadyActive" class="content">
          <p v-if="!isCurrentUser">{{ this.meta.activated.other_msg }}</p>
          
          <p v-if="isCurrentUser">{{ this.meta.activated.msg }}</p>
          
          <div v-if="isCurrentUser" class="input-group">
            <input type="text" v-model="secret" class="input-text">
            <button @click="deactivate" class="btn rounded-l-none" :disabled="isInValid">{{ this.meta.activated.button }}</button>
          </div>
        </div>
        
        <div v-if="!isCurrentUser && !isAlreadyActive" class="content">
          <p>{{ this.meta.activate.other_user_msg }}</p>
        </div>
      
        <transition name="two-fa-fade">
          <div v-if="isDisabled && isCurrentUser && !isAlreadyActive" class="activate flex flex-col items-center justify-center absolute top-0 right-0 bottom-0 left-0 z-10">
            <button @click="enabling = true" class="btn btn-primary">{{ this.meta.activate.enable.button }}</button>
            
            <div class="content pt-2">
              <p>{{ this.meta.activate.enable.description }}</p>
            </div>
          </div>
        </transition>
        
        <div v-if="isCurrentUser && !isAlreadyActive" class="activate-form">
          <img class="float-left" :src="meta.qrCode" />
          
          <div class="right-side float-left p-2">
            <div class="content">
              <p class="break-all"><strong>{{ this.meta.activate.key_label }}:</strong> {{ meta.key }}</p>
              <p class="break-all"><strong>{{ this.meta.activate.url_label }}:</strong> {{ meta.url }}</p>
            </div>
            
            <div class="pt-2">
              <label class="publish-field-label pb-2">{{ this.meta.activate.label }}</label>
              
              <div class="input-group">
                <input type="text" v-model="secret" class="input-text">
                <button @click="activate" class="btn btn-primary rounded-l-none" :disabled="isInValid">{{ this.meta.activate.button }}</button>
              </div>
            </div>
            
            <div class="content pt-2">
              <p>{{ this.meta.activate.get_app }} <button @click="showApps = 'android'">Android</button>, <button @click="showApps = 'ios'">iOS</button>.</p>
              
              <ul v-if="showApps === 'android'">
                <li v-for="(app, index) in this.meta.activate.android" :key="index"><a :href="app.url" target="_blank">{{ app.name }}</a></li>
              </ul>
              
              <ul v-if="showApps === 'ios'">
                <li v-for="(app, index) in this.meta.activate.ios" :key="index"><a :href="app.url" target="_blank">{{ app.name }}</a></li>
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
            secret: "",
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
          if (response.data.success === true) {
            this.$notify.success(this.meta.activate.activated);
            this.data = this.meta.key;
          } else {
            this.$notify.error(this.meta.activate.erorrs.code);
          }
        }).catch(e => {
          this.$notify.error(this.meta.activate.errors.unknown);
        }).finally(() => {
          this.enabling = false;
          this.secret = "";
          this.showApps = null;
        });
      },
      deactivate() {
        this.$axios.post(
          this.meta.actions.disable,
          {
            secret: this.secret,
          }
        ).then(response => {
          if (response.data.success === true) {
            this.$notify.success(this.meta.deactivate.disabled);
            this.data = null;
          } else {
            this.$notify.error(this.meta.deactivate.errors.code);
          }
        }).catch(e => {
          this.$notify.error(this.meta.deactivate.errors.unknown);
        }).finally(() => {
          this.secret = "";
        });
      },
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