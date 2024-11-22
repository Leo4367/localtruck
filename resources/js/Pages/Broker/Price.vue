<script setup>
import {Head} from "@inertiajs/vue3";
import {computed, defineProps, ref} from "vue";

const dataAll = defineProps({
    tableData: Array,
    columns: Array,
});


/*const rowLineHeight = ({row, rowIndex}) => {
    const minSpread = Math.min(
        ...dataAll.tableData
            .filter((item) => item.spread !== null) // Exclude null values
            .map((item) => item.spread)
    );
    if (row.spread === minSpread) {
        return 'background-color: #00dbfe;font-weight: bold;'
    }
}*/

/*function cellClassName({row, column}) {
    const brokerColumns = dataAll.columns.map((col) => col.prop);
    if (brokerColumns.includes(column.property)) {

        const prices = brokerColumns
            .map((prop) => row[prop])
            .filter((price) => price !== null);
        const minPrice = Math.min(...prices);

        return row[column.property] === minPrice ? "highlight-cell" : "";
    }
    return "";
}*/

function cellClassName({row, column}) {
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
                        :header-cell-style="{ textAlign: 'center' }"
                        :cell-style="cellClassName"
                    >
                        <el-table-column prop="customer_name" label="Name" width="180"/>
                        <el-table-column prop="deliver_address" label="Address"/>
                        <el-table-column prop="work_order" label="Work Order#"/>

                        <!-- 动态加载 Broker 列 -->
                        <el-table-column
                            v-for="column in dataAll.columns"
                            :key="column.prop"
                            :prop="column.prop"
                            :label="column.label"
                        />

                        <el-table-column
                            prop="spread"
                            label="差价($)"
                            sortable
                        />
                    </el-table>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
</style>
