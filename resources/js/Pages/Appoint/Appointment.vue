<script setup>
import {Head} from '@inertiajs/vue3';
import {defineProps} from 'vue';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

// 定义从后端传递过来的属性
const props = defineProps({
    appointments: Array // appointments 是一个数组
});
</script>

<template>
    <Head title="MyAppointment"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Appointment List</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <el-main>
                        <el-row :gutter="20">
                            <!-- 遍历appointments并展示每个预约的信息 -->
                            <el-col
                                :xs="24"
                                :sm="12"
                                :md="8"
                                :lg="6"
                                v-for="(appointment, index) in props.appointments"
                                :key="index"
                            >
                                <el-card class="car-card" shadow="hover">
                                    <template #header>
                                        <div class="card-header">
                                            <span>{{ appointment.warehouse.name }}</span>
                                        </div>
                                    </template>
                                    <div class="appointment-details">
                                        <div class="appointment-label">Time:</div>
                                        <div class="appointment-value">{{ appointment.time_slot }}</div>
                                    </div>
                                    <div class="appointment-details">
                                        <div class="appointment-label">Number:</div>
                                        <div class="appointment-value">{{ appointment.pickup_number }}</div>
                                    </div>
                                    <div class="appointment-details">
                                        <div class="appointment-label">Type:</div>
                                        <div class="appointment-value">{{ appointment.type }}</div>
                                    </div>
                                </el-card>
                            </el-col>
                        </el-row>
                    </el-main>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


<style scoped>
.car-card {
    margin-top: 20px;
    background-color: #409EFF; /* 设置卡片的背景颜色 */
    border-radius: 10px; /* 添加圆角 */
    color: white; /* 设置文字颜色为白色 */
    padding: 20px; /* 增加卡片的内边距 */
    transition: transform 0.3s, box-shadow 0.3s; /* 添加动画效果 */
    min-width: 250px; /* 添加卡片最小宽度 */
    max-width: 100%; /* 确保卡片在移动端的宽度不会超出容器 */
}

/* 卡片悬停时的效果 */
.car-card:hover {
    transform: scale(1.05); /* 悬停时稍微放大 */
    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2); /* 添加阴影效果 */
}

h2 {
    text-align: center;
}

.card-header {
    text-align: center;
    background-color: #a17bd3; /* 头部背景颜色 */
    color: white; /* 头部文字颜色 */
    padding: 10px;
    font-weight: bold;
    border-bottom: 2px solid white;
    border-radius: 10px 10px 0 0; /* 圆角头部 */
}

.appointment-details {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.appointment-label {
    font-weight: bold;
    margin-right: 10px;
}

.appointment-value {
    text-align: left;
}

/* 响应式：根据屏幕宽度调整列的布局 */
@media (max-width: 768px) {
    .el-col {
        padding: 0 10px;
    }

    .car-card {
        margin-bottom: 20px;
    }

    .appointment-details {
        flex-direction: column; /* 竖直显示在手机端 */
        text-align: left;
    }

    .appointment-label {
        margin-bottom: 5px;
    }
}
</style>
