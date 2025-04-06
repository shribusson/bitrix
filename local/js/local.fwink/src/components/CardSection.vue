<template>
  <div :id="'block' + block.ID" class="container-section">
    <div class="control-block" v-if="editMode && canEdit"
         :draggable="editMode"
         @dragstart="startDrag($event,'', block, 'block')"
         @dragend="dragEnd($event, '', block, 'block')"
         >
      <button class="ui-btn ui-btn-xs ui-btn-light ui-btn-icon-add" @click="addChildrenClick" title="Добавить дочерний блок"></button>
      <button class="ui-btn ui-btn-xs ui-btn-light ui-btn-icon-edit" @click="editBlockClick" title="Редактировать блок"></button>
      <button class="ui-btn ui-btn-xs ui-btn-light ui-btn-icon-remove" @click="removeBlockClick" title="Удалить блок"></button>
    </div>
    <div class="header" :style="customHeaderStyles">{{block.NUMBER}} {{ block.NAME }}</div>
    <div class="own-content"
         @dragenter="onDragEnter($event, '', '', mode)"
         @dragleave="onDragLeave($event, '')"
    >
      <!-- Окошко для перетаскивания поста -->
      <div v-if="block.ID" class="droppablePost"
           :class="{ready: (isDropActiveId === block.ID && (mode === 'post' || mode === 'block'))}"
           @drop='onDrop($event, "", block)'
           @dragover.prevent
      >
        <div class="droppable-overlay" v-if="isDropActiveId === block.ID && (mode === 'post' || mode === 'block')">
          <div class="droppable-overlay-title">{{dropAreaText}}</div>
        </div>
      </div>
      <CardPost :node-emp="block" :post-data="postData" v-for="postData in block.POSTS" :index="postData.id"/>
    </div>
    <div class="childs">
      <CardSection v-for="child in block.children"
                   :block="child"
                   @openpost="openPost"
                   @addchildren="addChildren"
                   @editblock="editBlock"
                   @removeblock="removeBlock"/>
    </div>
  </div>
</template>

<script>
import UserList from "./UserList.vue";
import { mapState, mapActions, mapGetters } from "vuex";
import CardPost from "./CardPost.vue";
export default {
  name: "CardSection",
  data() {
    return {
      isDropActive: 0,
      isDropPostActive: 0,
      /*dropAreaText: 'Переместить сотрудника'*/
    };
  },
  computed: {
    ...mapGetters(['editMode']),
    ...mapState([['mode'], ['departmentData'], ['currentScale'], ['isDropActiveId'], ['dropAreaText']]),
    canEdit() {
      // todo: rights
      return true;
    },
    customBlockStyles() {
      let style = {};
      if (this.block.COLOR_BLOCK.length) {
        style['background-color'] = this.block.COLOR_BLOCK;
      }
      return style;
    },
    customHeaderStyles() {
      let style = {};
      if (this.block.COLOR_HEADER.length) {
        style['background-color'] = this.block.COLOR_HEADER;
      }
      return style;
    },
    hide() {
      return this.block.hasOwnProperty('IS_HIDE') && this.block.IS_HIDE === true && !this.editMode;
    },
    /*postData() {
      let data = {
        id: this.block.ID_POST,
        name: this.block.NAME_POST,
        isManager: parseInt(this.block.SHIEF) > 0,
        functions: this.block.FUNCTIONS,
        ckp: this.block.CKP,
        users: this.block.hasOwnProperty('EMPLOYEE') ? this.block.EMPLOYEE : []
      }
      return data;
    }*/
  },
  props: ["block"],
  components: {CardPost, UserList},
  methods: {
    ...mapActions(["SET_DROP_ELEMENT"]),
    openPostClick(event) {
      this.openPost();
    },
    addChildrenClick(event) {
      this.addChildren();
    },
    editBlockClick(event) {
      this.editBlock();
    },
    removeBlockClick(event) {
      this.removeBlock();
    },
    openPost(id) {
      id = id || this.postData.id;
      this.$emit('openpost', id);
    },
    addChildren(id) {
      id = id || this.block.ID;
      this.$emit('addchildren', id);
    },
    editBlock(id) {
      id = id || this.block.ID;
      this.$emit('editblock', id);
    },
    removeBlock(id, name) {
      id = id || this.block.ID;
      name = name || this.block.NAME;
      this.$emit('removeblock', id, name);
    },
    /*Drag and Drop*/
    startDrag(event, item, parantBlock, mode) {
      console.log(event, item, parantBlock, mode)
      
      // console.log(document.querySelector("#target_block").innerText)
      // console.log(document.querySelector("#target_block").outerHTML)

      // if(mode == 'post') {
        
      //   if(this.editMode) {
      //     this.$store.commit('setDropMode', mode);
      //     if (event.getModifierState("Shift")) {
      //       this.dragMove(event, item, parantBlock, mode)
      //     }
      //     // if (event.getModifierState("Control")) {
      //     //   this.dragCopy(event, item, parantBlock, mode)
      //     // }
      //   } else {
      //     event.dataTransfer.dropEffect = 'none'
      //     event.dataTransfer.effectAllowed = 'none'
      //   }
      // } else 
      if(mode === 'block') {
        if(this.editMode) {
          this.$store.commit('setDropMode', mode);
          if (event.getModifierState("Shift")) {
            this.dragMove(event, item, parantBlock, mode)
          } else {
            this.dragMoveClear(event)
            event.dataTransfer.dropEffect = 'none'
            event.dataTransfer.effectAllowed = 'none'
            return false
          }
          // if (event.getModifierState("Control")) {
          //   this.dragCopy(event, item, parantBlock, mode)
          // }
        } else {
          event.dataTransfer.dropEffect = 'none'
          event.dataTransfer.effectAllowed = 'none'
        }
      } else {
        event.dataTransfer.dropEffect = 'none'
        event.dataTransfer.effectAllowed = 'none'
      }
      
    },
    dragEnd(event, elem, nodeEmp, mode) {
      let element = document.getElementById('clone')
      if(element) {
        element.parentNode.removeChild(element);
      }
    },
    dragMove(event, item, parantBlock, mode) {
      // if(mode == 'post') {
      //    // Создаем клон перетаскиваемого поста
      //   let elem = document.getElementById('post' + item.id)
      //   let clone = elem.cloneNode(true);
      //   // Масштабируем клона и назначаем стили
      //   clone.classList.add('targetBlock')
      //   clone.style.zoom = this.currentScale
      //   clone.style.width = 100 * this.currentScale + '%'
      //   clone.style.height = 100 * this.currentScale + '%'
      //   clone.style.background = parantBlock.COLOR_BLOCK
      //   // Даем клону id и засовываем в оригинал
      //   clone.id = 'clone'
      //   elem.prepend(clone);
      //   // Добавляем данные в event
      //   event.dataTransfer.setDragImage(clone, event.offsetX * this.currentScale , event.offsetY);
      //   event.dataTransfer.dropEffect = 'move'
      //   event.dataTransfer.effectAllowed = 'move'
      //   event.dataTransfer.setData('type', 'move')
      //   event.dataTransfer.setData('oldBlockID', parantBlock.ID)
      //   let post = JSON.stringify(item)
      //   event.dataTransfer.setData('post', post)
      // } else 
      if(mode === 'block') {
        console.log(this.block)
        // Создаем клон перетаскиваемого блока без панели управления
        let elem = document.getElementById('block' + this.block.ID)
        let clone = elem.cloneNode(true);
        let controlBlock = clone.querySelector('.control-block')
        controlBlock.parentNode.removeChild(controlBlock);
        // Масштабируем клона и назначаем стили
        clone.classList.add('targetBlock')
        clone.style.zoom = this.currentScale
        clone.style.width = 100 * this.currentScale + '%'
        clone.style.height = 100 * this.currentScale + '%'
        // Даем клону id и засовываем в оригинал
        elem.prepend(clone);
        clone.id = 'clone'
        // Добавляем данные в event
        event.dataTransfer.setDragImage(clone, event.offsetX * this.currentScale , event.offsetY);
        event.dataTransfer.dropEffect = 'move'
        event.dataTransfer.effectAllowed = 'move'
        event.dataTransfer.setData('oldBlockID', this.block.ID)
        event.dataTransfer.setData('type', 'move')
      }
    },
    onDrop(event, data, propsData) {
      console.log('event', event, 'data', data, 'propsData', propsData)
      if(this.mode === 'post') {
        if(this.editMode) {
          if (event.getModifierState("Shift")) {
            this.dropMovePost(event, data, propsData)
          }
          // if (event.getModifierState("Control")) {
          //   this.dropCopyPost(event, data, propsData)
          // }
          this.isDropActive = 0;
          this.$store.commit('setIsDropActiveId', 0);
          this.$store.commit('setIsDropActiveText', '');
          // this.$store.commit('setDropMode', '');
          console.log('onDropFinish', this.mode, this.isDropActive)
        }
      }
      if(this.mode === 'block') {
        if (event.getModifierState("Shift")) {
          this.dropMoveBlock(event, data, propsData)
        }
        // if (event.getModifierState("Control")) {
        //   this.dropCopyBlock(event, data, propsData)
        // }
        this.isDropActive = 0;
        this.$store.commit('setIsDropActiveId', 0);
        this.$store.commit('setIsDropActiveText', '');
        this.$store.commit('setDropMode', '');
      }
    },
    /*dropCopy(event, data, propsData) {
      // data - пост в который передали пользователя 
      // propsData - блок с постами
      event.preventDefault();
      const typeEvent = event.dataTransfer.getData('type')
      const oldPostID = event.dataTransfer.getData('oldPostID')
      const user = JSON.parse(event.dataTransfer.getData('user'))
      // typeEvent - скопировали
      // oldPostID - id поста от куда скопировали
      // user - пользователь которого скопировали
      console.log('typeEvent', typeEvent, 'oldPostID', oldPostID, 'user', user)
      if (data.id != oldPostID ) {
        let objectDrag = {
          typeEvent: typeEvent,
          oldPostId: oldPostID,
          newPostId: data.id,
          user: user
        }
        this.addDropUserPost(propsData, data.id, objectDrag.user, typeEvent)
        this.SET_DROP_ELEMENT(objectDrag);
      } else {

      }
    },
    dropMove(event, data, propsData) {
      // data - пост в который передали пользователя 
      // propsData - блок с постами
      event.preventDefault();
      const typeEvent = event.dataTransfer.getData('type')
      const oldPostID = event.dataTransfer.getData('oldPostID')
      const user = JSON.parse(event.dataTransfer.getData('user'))
      // typeEvent - перетащили
      // oldPostID - id поста от куда перетащили
      // user - пользователь которого перетащили
      console.log('typeEvent', typeEvent, 'oldPostID', oldPostID, 'user', user)
      if (data.id != oldPostID ) {
        let objectDrag = {
          typeEvent: typeEvent,
          oldPostId: oldPostID,
          newPostId: data.id,
          user: user
        }
        this.addDropUserPost(propsData, data.id, objectDrag.user, typeEvent)
        this.SET_DROP_ELEMENT(objectDrag);
      } else {
        let objectDrag = {
          typeEvent: typeEvent,
          oldPostId: oldPostID,
          newPostId: oldPostID,
          user: user
        }
        for(let i in propsData.POSTS) {
          if(propsData.POSTS[i].id == oldPostID) {
            propsData.POSTS[i].users.push(user);
            this.SET_DROP_ELEMENT(objectDrag);
          }
        }
      }
    },*/
    dropMovePost(event, data, propsData) {
      event.preventDefault();
      console.log('dropMovePost', data, propsData)
      const post = JSON.parse(event.dataTransfer.getData('post'))
      const oldBlockID = event.dataTransfer.getData('oldBlockID')
      const typeEvent = event.dataTransfer.getData('type')
      // this.addDropElem(propsData, '', post, typeEvent)
      if(propsData.ID != oldBlockID) {
        this.addDropUserPost(propsData, '', post, typeEvent)
        const moveData = {
          typeEvent: typeEvent,
          entityType: 'post',
          newBlockId: propsData.ID,
          oldBlockId: oldBlockID,
          post: post
        };
        this.SET_DROP_ELEMENT(moveData);
      } else {
        propsData.POSTS.push(post);
      }
    },
    addDropUserPost(container, postId, user, typeEvent) {
      console.log('container', container, 'postId', postId, 'user', user, 'typeEvent', typeEvent);
      if(typeEvent == 'move') {
        // перебираем все посты в контейнере
        for(let i in container.POSTS) {
          // находим тот в который хотели положить пользователя
          if(container.POSTS[i].id == postId) {
            // проверяем если в посте объект users
            if (container.POSTS[i].hasOwnProperty('users')) {
              console.log(container.POSTS[i].users)
              // перебираем всех userов
              for (let j in container.POSTS[i].users) {
                // userов со значение null удаляем
                if(container.POSTS[i].users[j] == null) {
                  console.log(container.POSTS[i].users[j])
                  container.POSTS[i].users.splice(j, 1);
                } 
              }
              let count = 0
              // перебираем всех userов
              for (let j in container.POSTS[i].users) {
                // если user уже есть увеличиваем счетчик
                if(container.POSTS[i].users[j].ID == user.ID) {
                  count++
                }
              }
              // если user отсутствует то добавляем
              if(count == 0) {
                container.POSTS[i].users.push(user);
              }
              console.log(container.POSTS[i].users)
              // container.POSTS[i].users.push(user);
            } else {
              container.POSTS[i].users = [];
              return container.POSTS[i].users.push(user)
            }
          }
        }
      } /*else if (typeEvent == 'copy') {
        // перебираем все посты в контейнере
        for(let i in container.POSTS) {
          // находим тот в который хотели положить пользователя
          if(container.POSTS[i].id == postId) {
            // проверяем если в посте объект users
            if (container.POSTS[i].hasOwnProperty('users')) {
              let count = 0
              // перебираем всех userов
              for (let j in container.POSTS[i].users) {
                // если user уже есть увеличиваем счетчик
                if(container.POSTS[i].users[j].ID == user.ID) {
                  count++
                }
              }
              // если user отсутствует то добавляем
              if(count == 0) {
                container.POSTS[i].users.push(user);
              }
            // так как users отсутствует создаем массив и добавляем туда user
            } else {
              container.POSTS[i].users = [];
              return container.POSTS[i].users.push(user)
            }
          }
        }
      }*/
      // container - блок с постами
      // postId - пост в который перетащили
      // user - пользователь которого перетащили
      
    },
    dropMoveBlock(event, data, propsData) {
      event.preventDefault();
      // console.log('dropMoveBlock', data, propsData)
      const oldBlockID = event.dataTransfer.getData('oldBlockID')
      const typeEvent = event.dataTransfer.getData('type')
      if(propsData.ID != oldBlockID) {
        //this.addDropElem(propsData, oldBlockID, '', typeEvent)
        const moveData = {
          typeEvent: typeEvent,
          entityType: 'block',
          newParentId: propsData.ID,
          blockId: oldBlockID
        };
        this.SET_DROP_ELEMENT(moveData);
      }
    },
    onDragEnter(event, postData) {
      if(this.editMode) {
        if(this.mode === 'post') {
          if (event.getModifierState("Shift")) {
            //s.isDropActive = postData.id;

            // this.isDropPostActive = this.nodeEmp.ID
            console.log('cardsection dragEnter');
            console.log(this.block.ID);
            this.$store.commit('setIsDropActiveId', this.block.ID);
            if(event.getModifierState("Shift")) {
              console.log('asdas')
              // this.dropAreaText = 'Поместить пост';
              this.$store.commit('setIsDropActiveText', 'Поместить должность');
            }
            // if(event.getModifierState("Control")) {
            //   this.dropAreaText = 'Скопировать пост';
            // }
          }
        }
        if(this.mode === 'block') {
          console.log('cardsection dragenter block')
          if (event.getModifierState("Shift")) {
            this.$store.commit('setIsDropActiveId', this.block.ID);
            this.$store.commit('setIsDropActiveText', 'Поместить блок');
          }
        }
      }
    },
    onDragLeave(event, postData) {
      console.log('cardsection onDragLeave')
      if(this.editMode) {
        if(this.mode == 'post') {
          if(!event.fromElement.classList.contains('droppable-overlay') &&
              !event.fromElement.classList.contains('droppable-overlay-title') &&
              !event.fromElement.offsetParent.classList.contains('postinfo') &&
              !event.fromElement.classList.contains('footer') &&
              !event.fromElement.classList.contains('open-link')) {
            // this.isDropPostActive = 0;
            this.$store.commit('setIsDropActiveId', 0);
            this.$store.commit('setIsDropActiveText', '');
          }
        }
        if(this.mode === 'block') {
          if(!event.fromElement.classList.contains('droppable-overlay') &&
              !event.fromElement.classList.contains('droppable-overlay-title')) {
            // this.isDropPostActive = 0;
            this.$store.commit('setIsDropActiveId', 0);
            this.$store.commit('setIsDropActiveText', '');
          }
        }
      }
    }
  }
}
</script>

<style scoped lang="scss">
.container-section {
  padding: 10px 5px;
  position: relative;

  &:hover {
    //box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
    box-shadow: -1px 2px 4px 4px rgba(34, 60, 80, 0.56);

    .control-block {
      display: flex;
      align-items: center;
      justify-content: flex-end;
    }
  }

  .control-block {
    position: absolute;
    width: 100%;
    top: 0;
    left: 0;
    height: 25px;
    display: none;
    background: rgba(0,0,0,0.85);
  }

  .header {
    /*background: #312F3F;*/
    height: 25px;
    padding: 0 10px;
    display: flex;
    align-items: center;
    font-size: 10px;
    color: #131313;
    text-transform: uppercase;
    font-weight: bold;
    font-family: OpenSans-Semibold, "Helvetica Neue", Arial, Helvetica, sans-serif;
    cursor: default;
  }

  .own-content {
    position: relative;
  }

  .droppablePost {
    height: calc(100% - 4px);
    width: calc(100% - 4px);
    position: absolute;

    &.ready {
      z-index: 99;
    }
  }
  .postinfoPostEmpty {
    min-height: 75px;
    width: 100%;
  }
  .droppable-overlay {
    border-radius: 8px;
    border: 2px dashed #f5f5f5;
    background: rgba(0, 0, 0, 0.2);
    width: 100%;
    position:absolute;
    top: 0;
    left: 0;
    height: 100%;
    // margin-top: 25px;
    .droppable-overlay-title {
      position: absolute;
      text-align: center;
      color: #f5f5f5;
      font-size: 26px;
      font-weight: 400;
      top: calc(50% - 15px);
      width: 100%;
    }
  }

  /*.postinfo {
    padding: 0 10px;

    .title {
      padding: 10px 0;

      a {
        display: block;
        color: #131313;
        text-decoration: none;
        font-family: OpenSans-Regular, "Helvetica Neue", Arial, Helvetica, sans-serif;
        font-size: 12px;
      }
    }

    .functions {
      font-family: OpenSans-Regular,"Helvetica Neue", Arial, Helvetica, sans-serif;
      font-size: 9px;

      .titles {
        font-style: italic;
        font-size: 10px;
        padding: 10px 0;
      }
      .content {

      }
    }

    &.droppable {
      border: 2px dashed #f5f5f5;
      border-radius: 8px;
      position:relative;

      div {
        pointer-events: none;
      }

      .droppable-overlay {
        background: rgba(0, 0, 0, 0.2);
        width: 100%;
        height: 100%;
        position:absolute;
        top: 0;
        left: 0;

        .droppable-overlay-title {
          position: absolute;
          text-align: center;
          color: #f5f5f5;
          font-size: 16px;
          font-weight: 400;
          top: calc(50% - 10px);
          width: 100%;
        }
      }
    }
  }*/

  .childs {
    padding-bottom: 10px;
    border-bottom: 1px solid #DDDDDD;
  }
}
</style>