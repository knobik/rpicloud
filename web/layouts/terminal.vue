<template>
  <div class="terminal-padding">
    <nuxt />
  </div>
</template>

<script>
export default {
  middleware: 'authenticated',
  mounted () {
    // hacky
    [
      document.body,
      document.documentElement,
      document.getElementById('__nuxt'),
      document.getElementById('__layout')
    ].forEach((element) => {
      element.classList.add('terminal-body')
    })
  }
}
</script>

<style type="scss">
.terminal-body {
  height: 100%;
  min-height: 100%;
}

.terminal-padding {
  height: 100%;
  width: auto;
  box-sizing: border-box;
  padding: 5px;
  background-color: black;
}
</style>
