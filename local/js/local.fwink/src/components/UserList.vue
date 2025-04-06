<template>
  <div ref="usersblock">
    <div class="preview">
      <User v-for="user in previewUsers" :user="user" :is-large="(isManager || previewUsers.length === 1)" :postId="postId" v-on:removeUser="removeUser($event)"/>
    </div>
    <div class="footer" v-if="(!isManager && !hideUserLink)">
      <a draggable="false" href="javascript: void(0);" class="open-link" @click="showAllUsers">{{linkText}}</a>
      <div class="popup" v-if="showAll">
        <div class="scroll">
          <User v-for="user in users" :user="user" is-large="true" :postId="postId" v-on:removeUser="removeUser($event)"/>
        </div>
        <div class="arrow"></div>
      </div>
    </div>
  </div>
</template>

<script>
import User from "./User.vue";
import declOfNum from "../scripts/declofnum.js";
export default {
  name: 'UserList',
  props: ['users', 'isManager', 'postId','hideUserLink'],
  components: {User},
  data() {
    return {
      showAll: false
    }
  },
  computed: {
    previewUsers() {
      return this.users.slice(0,10);
    },
    linkText() {
      return Object.entries(this.users).length + ' ' + declOfNum(Object.entries(this.users).length, ['сотрудник', 'сотрудника', 'сотрудников']);
    }
  },
  methods: {
    showAllUsers() {
      this.showAll = !this.showAll;
      if(this.showAll) {
        this.bindHide();
        //this.$refs.usersblock.closest('.container').style.zIndex = 500;
      } else {
        this.unbindHide();
        //this.$refs.usersblock.closest('.container').style.zIndex = 'auto';
      }
    },
    bindHide() {
      document.addEventListener('click', this.hideHandler, true);
    },
    unbindHide() {
      document.removeEventListener('click', this.hideHandler, true);
    },
    hideHandler(event) {
      if(event.target !== this.$refs.usersblock && !this.$refs.usersblock.contains(event.target)) {
        this.showAll = false;
        //this.$refs.usersblock.closest('.container').style.zIndex = 0;
        this.unbindHide();
      }
    },
    removeUser(event) {
      this.users.forEach((item, index) => {
        if (item.ID == event) {
          this.users.splice(index, 1)
        }
      })
      return this.users
    }
  }
}
</script>

<style scoped lang="scss">
.preview {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  flex-wrap: wrap;
}
.footer {
  padding: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  position:relative;

  .popup {
    position: absolute;
    width: 200px;
    padding: 20px 10px;
    background: #ffffff;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
    top: 40px;
    z-index: 10;

    .scroll {
      height: 100%;
      max-height: 300px;
      overflow: auto;
    }

    .arrow {
      width: 33px;
      height: 22px;
      display: block;
      position: absolute;
      top: -21px;
      left: calc(50% - 11px);
      overflow: hidden;

      &:before {
        background-color: #fff;
        box-shadow: 0 0 20px rgba(0,0,0,0.2);
        content: '';
        height: 15px;
        position: absolute;
        left: 9px;
        top: 16px;
        -webkit-transform: rotate(
                45deg
        );
        -ms-transform: rotate(45deg);
        transform: rotate(
                45deg
        );
        -webkit-transform-origin: 50% 50%;
        -ms-transform-origin: 50% 50%;
        transform-origin: 50% 50%;
        width: 15px;
      }
    }
  }
}
.open-link {
  color: #726C9B;
  text-decoration: none;
  font-family: OpenSans-Regular, "Helvetica Neue", Arial, Helvetica, sans-serif;
  font-size: 9px;
}
</style>