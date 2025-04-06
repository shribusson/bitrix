<template>
  <div class="myModal">
    <p>{{ title }}</p>
    <img :src="node.logo" />
    <div>
      {{ node.name }}
    </div>
    <div>
      {{ node.position }}
    </div>
  </div>
</template>

<script>
export default {
  name: "UnitModal",
  data() {
    return {
      title: "Информация о сотруднике",
    };
  },
  methods: {},
  watch: {},
  components: {},
  props: ["node"],
};
</script>

<style scoped lang="scss">
.myModal {
  padding: 8px;
  background: #039be5;
  border: 2px solid #b3e5fc;
}
</style>