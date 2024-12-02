<script setup>

const tableData = [
    {
        date: '2016-05-03',
        name: 'Tom',
        address: 'No. 189, Grove St, Los Angeles',
    },
    {
        date: '2016-05-02',
        name: 'Tom',
        address: 'No. 189, Grove St, Los Angeles',
    },
    {
        date: '2016-05-04',
        name: 'Tom',
        address: 'No. 189, Grove St, Los Angeles',
    },
    {
        date: '2016-05-01',
        name: 'Tom',
        address: 'No. 189, Grove St, Los Angeles',
    },
];

const tableRowClassName = ({row, rowIndex}) => {
    if (rowIndex === 1) {
        return 'warning-row';
    } else if (rowIndex === 3) {
        return 'success-row';
    }
    return '';
};

const tableRowStyle = ({row, rowIndex}) => {
    if (rowIndex === 1) {
        return {
            backgroundColor: "#8dc781",
            fontWeight: "bold", // 粗体
            textAlign: "center", // 居中
        };
    } else if (rowIndex === 3) {
        return {
            backgroundColor: "#f2ae78",
            fontWeight: "bold", // 粗体
            textAlign: "center", // 居中
        };
    }
    return {
        textAlign: 'center',
    };
}
</script>

<template>
    <el-table
        :data="tableData"
        style="width: 100%"
        :row-class-name="tableRowClassName"
        :row-style="tableRowStyle"
        border
        :header-cell-style="{ textAlign: 'center',backgroundColor:'#000000',fontWeight:'blod',color:'#ff8e00' }"
    >
        <el-table-column prop="date" label="Date" width="180"/>
        <el-table-column prop="name" label="Name" width="180"/>
        <el-table-column prop="address" label="Address"/>
    </el-table>
</template>


<style scoped>

</style>
