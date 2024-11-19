<script setup>
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale
} from 'chart.js'
import { Bar } from 'vue-chartjs'
import {ref} from "vue";


ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend)

const data = ['一月份', '二月份', '三月份','四月份', '五月份', '六月份', '七月份'];
const options = ref([]);

</script>

<template>
    <Bar :data="data" :options="options" />
</template>

<style scoped>

</style>
