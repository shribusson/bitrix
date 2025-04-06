<template>
    <div class="helper">
        <div class="help_block">
            <div class="help" @click="popupActive = true">
            &quest;
            </div>
        </div>
        <div class="popup_block">
            <div class="popup" v-if="popupActive">
                <div class="popup_content">
                    <h2>Подсказка</h2>
                    <span @click="popupActive = false">x</span>
                    <div class="popup_content__text">
                        <h4>Перемещение блоков</h4>
                        <p>Для перемешения блоков наведитесь курсором мыши на заголовок блока, 
                        зажмите &#8679; Shift и ЛКМ и потяните в блок которых вы хотите поместить, 
                        после отпустите сначала ЛКМ затем &#8679; Shift</p>
                    </div>
                    <div class="popup_content__text">
                        <h4>Перемещение должностей</h4>
                        <p>Для перемешения должностей наведитесь курсором мыши на заголовок должности,
                        зажмите &#8679; Shift и ЛКМ и потяните в блок в который хотите добавить должность,
                        после отпустите сначала ЛКМ затем &#8679; Shift</p>
                    </div>
                    <div class="popup_content__text">
                        <h4>Перемещение сотрудников</h4>
                        <p>Для перемещения сотрудников наведитесь курсором мыши иконку сотрудника,
                        зажмите &#8679; Shift и ЛКМ и потяните в должность в которую хотите добавить сотрудника,
                        после отпустите сначала ЛКМ затем &#8679; Shift</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
  name: "helpBlock",
  data() {
    return {
        popupActive: false,
    };
  },
};
</script>

<style scoped lang="scss">

.helper {
    position: fixed;
    width: 100%;
    z-index: 999;
    top: 0;
    height: 0;
}
.help_block {
  position: fixed;
  top: 0;
  left: calc(100% - 70px);
  width: max-content;
  z-index: 999;
  padding-top: 125px;
  height: 0;
}
.help {
  
  z-index: 999;
  width: 50px;
  height: 50px;
  border-radius: 50px;
  border: 2px solid #1b58ce;
  background-color: #ffffff;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 35px;
  font-family: OpenSans-Semibold, "Helvetica Neue", Arial, Helvetica, sans-serif;
  color: #1b58ce;
}

.help:hover {
  background-color: #1b58ce;
  color: white;
}

.popup_block {
  position: sticky;
  top: 0px;
  left: calc(50% - 300px);
  width: max-content;
  z-index: 999;
  padding-top: 10px;
  height: 0;
}
.popup {
  
  width: 600px;
  height:550px;
  
  
  border-radius: 25px;
  // border: 1px solid black;
  background: white;
  font-family: OpenSans-Semibold, "Helvetica Neue", Arial, Helvetica, sans-serif;
  display: flex;
  justify-content: center;
  z-index: 999;
}

.popup_content {
  position: relative;
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: middle;
  flex-direction: column;
  padding: 20px;
}

.popup_content h2{
  text-align: center;
}

.popup_content__text p{
  font-size: 14px;
}

.popup_content__text h4{
  font-family: cursive;
}

.popup_content span {
  position: absolute;
  top: 10px;
  right: 15px;
  color: #1b58ce;
  width: 25px;
  height: 25px;
  background: white;
  display: flex;
  justify-content: center;
  border-radius: 15px;
  border: 1px solid #1b58ce;
  cursor: default;
}

.popup_content span:active {
  color: white;
  background: #1b58ce;
}

</style>