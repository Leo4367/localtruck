<template>
    <div>
        <el-button mb-2 @click="toggle">Switch Language</el-button>
        <br />

        <el-config-provider :locale="locale">
            <el-table mb-1 :data="[]" />
            <el-pagination :total="100" />
            <el-card>this is a page</el-card>
        </el-config-provider>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import zhCn from 'element-plus/dist/locale/zh-cn.mjs'
import en from 'element-plus/dist/locale/en.mjs'

const language = ref('en')
const locale = computed(() => (language.value === 'zh-cn' ? zhCn : en))

const toggle = () => {
    language.value = language.value === 'zh-cn' ? 'en' : 'zh-cn'
}
</script>
