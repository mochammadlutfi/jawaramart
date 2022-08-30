<template>
  <input ref="inputRef" />
</template>

<script>
import { watch } from 'vue' // Vue 2: import { watch } from '@vue/composition-api'
import { useCurrencyInput } from 'vue-currency-input'

export default {
  name: 'CurrencyInput',
  props: {
    modelValue: Number, // Vue 2: value
    options: Object
  },
  setup(props) {
    const options = { 
        currency: 'IDR',
        locale : 'id-ID',
        symbol : 'Rp',
        currencyDisplay : "symbol",
        hideCurrencySymbolOnFocus : false,
        hideGroupingSeparatorOnFocus : false,
        hideNegligibleDecimalDigitsOnFocus : false,
        autoDecimalDigits : false,
        autoSign : false,
        useGrouping : true,
        accountingSign : true

    };
    const { inputRef, setOptions, setValue } = useCurrencyInput(options)

    watch(
      () => props.modelValue, // Vue 2: props.value
      (value) => {
        setValue(value)
      }
    )

    return { inputRef }
  }
}
</script>