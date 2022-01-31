<template>
  <Head :title="title ? `${title} - ${ $page.props.settings.app_name }` : $page.props.settings.app_name">
    <slot />
  </Head>
</template>

<script>
import { Head } from '@inertiajs/inertia-vue'

export default {
  components: {
    Head,
  },
  props: {
    title: String,
  },
}
</script>