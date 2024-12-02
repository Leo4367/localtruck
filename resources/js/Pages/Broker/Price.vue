<script setup>
import {Head} from "@inertiajs/vue3";
import {computed, defineProps, ref} from "vue";
import TableData from "@/Components/TableData.vue";

const dataAll = defineProps({
    tableData: Array,
    columns: Array,
});

/*function cellClassName({row, column}) {
    // Get the column props from dynamic columns
    const brokerColumns = dataAll.columns.map((col) => col.prop); // All dynamic broker columns

    // Check if the column is part of broker pricing
    if (brokerColumns.includes(column.property)) {
        // Extract broker prices for the current row
        const prices = brokerColumns
            .map((prop) => row[prop])
            .filter((price) => price !== null && price !== undefined); // Filter valid prices

        // Determine the minimum price
        const minPrice = Math.min(...prices);

        // If this cell matches the minimum price, style it
        if (row[column.property] === minPrice) {
            return {backgroundColor: "#8dc781", fontWeight: "bold", textAlign: "center"}; // Highlighted style
        }
    }

    // Default styling
    return {textAlign: "center"};
}*/

function cellClassName({row, column}) {
    // 遍历所有公司列的 children，生成 broker 列的完整结构
    const brokerColumns = dataAll.columns.flatMap((col) =>
        col.children.map((child) => ({
            prop: child.prop, // broker 的 prop 名
            company: col.label, // 所属公司
        }))
    );

    // 找到当前列对应的 broker 信息
    const currentBroker = brokerColumns.find((broker) => broker.prop === column.property);

    if (!currentBroker) {
        // 如果不是 broker 列，使用默认样式
        return {textAlign: "center"};
    }

    // 获取当前公司所有 broker 的报价
    const companyBrokers = brokerColumns
        //.filter((broker) => broker.company === currentBroker.company) // 只取同一公司的列
        .map((broker) => row[broker.prop]) // 获取当前行的报价数据
        .filter((price) => price !== null && price !== undefined); // 筛选出有效报价


    // 确定当前公司的最低报价
    const minPrice = Math.min(...companyBrokers);

    // 如果当前单元格的值等于最低报价，则高亮
    if (row[column.property] === minPrice) {
        return {
            backgroundColor: "#8dc781", // 高亮背景色
            fontWeight: "bold", // 粗体
            textAlign: "center", // 居中
        };
    }

    // 默认样式
    return {textAlign: "center"};
}

</script>

<template>
    <Head title="Price"/>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="broker">
                    <el-table
                        class="broker-price"
                        :data="dataAll.tableData"
                        border
                        :header-cell-style="{ textAlign: 'center',backgroundColor:'#ffffff',fontWeight:'blod',color:'#000000' }"
                        :cell-style="cellClassName"
                    >
                        <el-table-column prop="customer_name" label="Name" width="180"/>
                        <el-table-column prop="deliver_address" label="Address"/>
                        <el-table-column prop="work_order" label="Work Order#"/>

                        <!-- 动态嵌套公司名与 broker 的表头 -->
                        <el-table-column
                            v-for="company in dataAll.columns"
                            :key="company.label"
                            align="center"
                        >
                            <!-- 一级表头（公司名） -->
                            <template #header>
                                {{ company.label }}
                            </template>

                            <!-- 二级表头（broker 名称） -->
                            <el-table-column
                                v-for="broker in company.children"
                                :key="broker.prop"
                                :prop="broker.prop"
                                :label="broker.label"
                            />
                        </el-table-column>

                        <el-table-column
                            prop="spread"
                            label="差价($)"
                            sortable
                        />
                    </el-table>
                </div>
<!--                <div class="table-test" style="margin-top: 50px;">
                    <TableData/>
                </div>-->
            </div>
        </div>
    </div>
</template>

<style scoped>
</style>
