<script setup>
import { Head } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ElMessage, ElMessageBox } from 'element-plus';
import axios from "axios";

const tableData = ref([]);
const columns = ref([]);

const fetchTableData = async () => {
    try {
        const response = await axios.get("/get-tabledata");
        tableData.value = response.data.tableData;
        columns.value = response.data.columns;
    } catch (error) {
        ElMessage.error("Failed to load table data");
    }
};

onMounted(() => {
    fetchTableData();
});

// 低价高亮
function cellClassName({ row, column }) {
    // 使用 columns.value 来代替 dataAll.columns
    const brokerColumns = columns.value.flatMap((col) =>
        col.children.map((child) => ({
            prop: child.prop, // broker 的 prop 名
            company: col.label, // 所属公司
        }))
    );

    // 找到当前列对应的 broker 信息
    const currentBroker = brokerColumns.find((broker) => broker.prop === column.property);

    if (!currentBroker) {
        // 如果不是 broker 列，使用默认样式
        return { textAlign: "center" };
    }

    // 获取当前公司所有 broker 的报价
    const companyBrokers = brokerColumns
        .map((broker) => row[broker.prop]) // 获取当前行的报价数据
        .filter((price) => price !== null && price !== undefined); // 筛选出有效报价

    // 确定当前公司的最低报价
    const minPrice = Math.min(...companyBrokers);

    // 如果当前单元格的值等于最低报价，则高亮
    if (parseFloat(row[column.property]) === minPrice) {
        return {
            backgroundColor: "#56b7c9", // 高亮背景色
            fontWeight: "bold", // 粗体
            textAlign: "center", // 居中
        };
    }

    // 默认样式
    return { textAlign: "center" };
}

const cell_dblclick = (row, column) => {
    const brokerName = column.property + '_id';
    const inquiry_price_id = row[brokerName];

    ElMessageBox.prompt('Please input price', 'Tip', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        inputPattern: /\d+(\.\d+)?$/,
        inputErrorMessage: 'Invalid Price',
    }).then(({value}) => {
        // 发送PUT请求到后台更新价格数据
        axios.post(`/update-price`, {
            id: inquiry_price_id,
            price: value,
        })
            .then((response) => {
                if (response.status === 200) {
                    // 后台更新成功，重新获取表格数据以刷新表格
                    ElMessage({
                        type: 'success',
                        message: 'Price updated successfully',
                    });
                    //刷新表格数据，不是刷新整个页面
                    fetchTableData();
                } else {
                    ElMessage({
                        type: 'error',
                        message: 'Failed to update price',
                    });
                }
            })
            .catch((error) => {
                ElMessage({
                    type: 'error',
                    message: 'Error occurred while updating price: ' + error.message,
                });
            });
    })
        .catch(() => {
            ElMessage({
                type: 'info',
                message: 'Input canceled',
            });
        });
}
</script>

<template>
    <Head title="Price"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight" style="text-align: center">Price List</h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="broker">
                        <el-table
                            class="broker-price"
                            :data="tableData"
                            border
                            @cell-dblclick="cell_dblclick"
                            :header-cell-style="{ textAlign: 'center', backgroundColor:'#a1d3c5', fontWeight:'blod', color:'#000000' }"
                            :cell-style="cellClassName"
                        >
                            <el-table-column prop="customer_name" label="Name" width="180"/>
                            <el-table-column prop="deliver_address" label="Address"/>
                            <el-table-column prop="work_order" label="Work Order#"/>

                            <!-- 动态嵌套公司名与 broker 的表头 -->
                            <el-table-column
                                v-for="company in columns"
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
                                >
                                </el-table-column>
                            </el-table-column>

                            <el-table-column
                                prop="spread"
                                label="差价 ($)"
                                sortable
                            />
                        </el-table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
table {
    background: linear-gradient(to bottom, #f8ffe8 0%, #e3f5ab 23%, #b7df2d 100%);
}
</style>
