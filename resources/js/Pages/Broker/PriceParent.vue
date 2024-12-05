<template>
    <div>
        <!-- 传递表格数据和列数据到子组件 -->
        <TempPrice :tableData="tableData" :columns="columns" @update-price="fetchTableData" />
    </div>
</template>

<script setup>
import {onMounted, ref} from 'vue';
import axios from 'axios';
import TempPrice from "@/Pages/Broker/TempPrice.vue";

const tableData = ref([]);
const columns = ref([
    // 这里定义实际的列数据结构，和之前传递给子组件的保持一致
]);

const fetchTableData = async () => {
    try {
        // 发送请求获取最新的表格数据，假设接口地址是 /get-table-data，根据实际调整
        const response = await axios.get('/tempprice');
        if (response.status === 200) {
            tableData.value = response.data;
        }
    } catch (error) {
        console.log('获取表格数据出错：', error);
    }
};

// 可以在合适的地方调用 fetchTableData 方法来初始化获取数据，比如 mounted 钩子等
onMounted(() => {
    fetchTableData();
});
</script>
