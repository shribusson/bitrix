<template>
  <div class="loader" id="loader" :class="{active : active}">
    <div id="cube-loader">
      <div class="caption">
        <div class="loader-description"></div>
        <div class="cube-loader">
          <div class="cube loader-1"></div>
          <div class="cube loader-2"></div>
          <div class="cube loader-4"></div>
          <div class="cube loader-3"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "preloader",
  props: ['active']
}
</script>

<style scoped>
.loader {
  display: none;
  position: absolute;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  background: rgba(255, 255, 255, 0.7);
  z-index: 600;
}
.loader.active {
  display: block;
}

#cube-loader {

  display: flex;
  height: 70%;
  width: 100%;
  position: absolute;
  top: 150px;
}
#cube-loader .caption {
  margin: 0 auto;
}
#cube-loader .cube-loader {
  width: 73px;
  height: 73px;
  margin: 0 auto;
  margin-top: 49px;
  position: relative;
  transform: rotateZ(45deg);
}
#cube-loader .cube-loader .cube {
  position: relative;
  transform: rotateZ(45deg);
  width: 50%;
  height: 50%;
  float: left;
  transform: scale(1.1);
}
#cube-loader .cube-loader .cube:before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: #2fc6f6;
  animation: cube-loader 2.76s infinite linear both;
  transform-origin: 100% 100%;
}
#cube-loader .cube-loader .loader-2 {
  transform: scale(1.1) rotateZ(90deg);
}
#cube-loader .cube-loader .loader-3 {
  transform: scale(1.1) rotateZ(180deg);
}
#cube-loader .cube-loader .loader-4 {
  transform: scale(1.1) rotateZ(270deg);
}
#cube-loader .cube-loader .loader-2:before {
  animation-delay: 0.35s;
}
#cube-loader .cube-loader .loader-3:before {
  animation-delay: 0.69s;
}
#cube-loader .cube-loader .loader-4:before {
  animation-delay: 1.04s;
}

@keyframes cube-loader {
  0%, 10% {
    transform: perspective(136px) rotateX(-180deg);
    opacity: 0;
  }
  25%, 75% {
    transform: perspective(136px) rotateX(0deg);
    opacity: 1;
  }
  90%, 100% {
    transform: perspective(136px) rotateY(180deg);
    opacity: 0;
  }
}
</style>