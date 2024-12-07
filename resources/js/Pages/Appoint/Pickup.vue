<script setup>
import TextInput from "@/Components/TextInput.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import {Head, useForm, usePage} from "@inertiajs/vue3";
import {onMounted, ref} from 'vue';
import axios from 'axios';
import {format} from 'date-fns';  // 用于格式化日期
import {ElMessage} from 'element-plus';

const {props} = usePage();

const form = useForm({
    appt_number: '',
    phone_number: '',
    po_number: '',
    driver_name: '',
    time_slot: '',
    warehouse_id: '',
    type: 'Pickup',
    dock_number: '',
    vehicle_type: '',
});


// 自动填充用户信息（如果已登录）
onMounted(() => {
    if (props.auth && props.auth.user) {
        form.driver_name = props.auth.user.name || '';
        form.phone_number = props.auth.user.phone_number || '';
    }
});
// 存储已预约和可用时间段
const bookedSlots = ref([]);
const availableTimeSlots = ref([]);
const value1 = ref(null);
const forbiddenDates = ref([]);  // 存储禁用日期
const options = ref([]);
const docks = {1: 42, 2: 44,};

const showSlotDate = (t) => {
    return format(t, 'HH:mm');
};

//根据日期获取时段
const chooseTimeSlot = (t) => {
    if (t !== null) {
        form.time_slot = '';
        try {
            //从后端获取可预约时间
            axios.get(route('appointment.booked-slots'),
                {params: {type: 'Pickup', slot: t, warehouse_id: form.warehouse_id, dock_number: form.dock_number}}
            ).then(function (response) {
                bookedSlots.value = response.data['booked'];
                //optionTime.value = allTimeSlots.map(slot => `${t} ${slot}`);
                availableTimeSlots.value = response.data['allTimeSlots'];
            });
        } catch (error) {
            console.error("Appointment time error", error);
        }
    }
}

// 获取禁用日期
const getForbiddenDates = (house) => {
    value1.value = null;
    axios.get(route('appointment.forbidden-dates'), {params: {type: 'Pickup', warehouse_id: house}})
        .then(response => {
            forbiddenDates.value = response.data;  // 假设返回的是禁用日期数组
        })
        .catch(error => {
            console.error("Error fetching forbidden dates", error);
        });
};

// 检查某个时间段是否已预约
const isBooked = (slot) => {
    return bookedSlots.value.includes(slot);
};

// 动态禁用日期
const disabledDate = (date) => {
    const todayTimestamp = Date.now() - (3600 * 1000 * 24); // 今天之前的时间戳
    const formattedDate = format(date, 'yyyy-MM-dd');     // 将日期格式化为 'yyyy-MM-dd'

    // 条件1: 禁用今天之前的日期
    const isBeforeToday = date.getTime() < todayTimestamp;

    // 条件2: 禁用后端传过来的日期
    const isForbiddenDate = forbiddenDates.value.includes(formattedDate);

    // 返回 true 禁用日期，满足任一条件则禁用
    return isBeforeToday || isForbiddenDate;
};

// 提交表单
const submit = () => {
    if (!form.warehouse_id) {
        form.errors.warehouse_id = "Warehouse is required"; // 设置错误信息
        return; // 阻止提交
    }
    form.post(route('appointment.store'), {
        onSuccess: () => {
            ElMessage({
                message: 'Appointment successfully created.',
                type: 'success',
            });
        },
        onError: (errors) => {
            // 获取并显示详细的错误信息
            if (errors.response && errors.response.data && errors.response.data.errors) {
                const errorMessages = Object.values(errors.response.data.errors)
                    .map(errorArray => errorArray.join(', '))  // 每个字段的错误信息可能是数组，转换为字符串
                    .join('\n');  // 将多个错误信息连接起来，以换行符分隔

                ElMessage.error(`Error creating appointment:\n${errorMessages}`);
            } else {
                ElMessage.error('Error creating appointment'); // 默认错误信息
            }
        },
    });
};

// 加载仓库列表
const loadWarehouseList = (dockNumber) => {
    if (!dockNumber) {
        options.value = []; // 重置仓库列表
        return;
    }
    axios.get(route('appointment.warehouse'))
        .then(response => {
            options.value = response.data; // 更新仓库列表
        })
        .catch(error => {
            console.error('Error fetching warehouse list:', error);
        });
};

// 当 Dock Number 改变时清空 Warehouse 并加载新列表
const chooseDock = (dock_value) => {
    form.dock_number = dock_value; // 更新当前选中的 Dock Number
    form.warehouse_id = ''; // 清空已选的 Warehouse
    loadWarehouseList(dock_value); // 根据 Dock 加载对应的 Warehouse 列表
};

</script>

<template>
    <GuestLayout>
        <Head title="Pickup"/>

        <form @submit.prevent="submit">
            <!-- Name Input -->
            <div>
                <InputLabel for="driver_name" value="Company Name"/>
                <TextInput
                    id="driver_name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.driver_name"
                    required
                    autofocus
                    autocomplete="driver_name"
                />
                <InputError class="mt-2" :message="form.errors.driver_name"/>
            </div>
            <!-- Phone Number Input (as 'tel' type) -->
            <div class="mt-4">
                <InputLabel for="phone_number" value="Phone Number"/>
                <TextInput
                    id="phone_number"
                    type="tel"
                    class="mt-1 block w-full"
                    v-model="form.phone_number"
                    required
                    autocomplete="tel"
                />
                <InputError class="mt-2" :message="form.errors.phone_number"/>
            </div>
            <!-- Pickup Number Input -->
            <div class="mt-4">
                <InputLabel for="appt_number" value="Pickup Number"/>
                <TextInput
                    id="appt_number"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.appt_number"
                    required
                    autofocus
                />
                <InputError class="mt-2" :message="form.errors.appt_number"/>
            </div>
            <div class="mt-4">
                <InputLabel for="po_number" value="PO Number"/>
                <TextInput
                    id="po_number"
                    type="tel"
                    class="mt-1 block w-full"
                    v-model="form.po_number"
                    required
                    autocomplete="tel"
                />
                <InputError class="mt-2" :message="form.errors.po_number"/>
            </div>
            <!-- Vehicle Type Input-->
            <div class="mt-4">
                <InputLabel for="vehicle_type" value="Vehicle Type" />
                <TextInput
                    id="vehicle_type"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.vehicle_type"
                    required
                />
                <InputError class="mt-2" :message="form.errors.vehicle_type" />
            </div>
            <!-- Dock Number Input -->
            <div class="mt-4">
                <InputLabel for="dock_number" value="Dock Number"/>
                <el-select
                    v-model="form.dock_number"
                    placeholder="Select Dock#"
                    size="large"
                    @change="chooseDock"
                >
                    <el-option
                        v-for="(dock,index) in docks"
                        :key="index"
                        :label="dock"
                        :value="dock"
                    ></el-option>
                </el-select>
                <InputError class="mt-2" :message="form.errors.dock_number"/>
            </div>

            <!--warehouse -->
            <div class="mt-4">
                <InputLabel for="warehouse_id" value="Warehouse"/>
                <el-select
                    v-model="form.warehouse_id"
                    placeholder="Select Warehouse"
                    size="large"
                    :disabled="!form.dock_number"
                    @change="getForbiddenDates"
                >
                    <el-option
                        v-for="(item,index) in options"
                        :key="index"
                        :label="item.name"
                        :value="String(item.id)"
                    >
                        <!--添加仓库选项的地址描述-->
                        <template #default>
                            <el-tooltip :content="`${item.address}`" placement="right">
                                <span>{{ item.name }}</span>
                            </el-tooltip>
                        </template>
                    </el-option>
                </el-select>
                <InputError class="mt-2" :message="form.errors.warehouse_id"/>
            </div>

            <!-- 时间段选择框 (动态加载可用时间段) -->
            <div class="mt-4">
                <InputLabel for="date-default" value="Pick Day"/>
                <el-date-picker
                    class="mt-1 block w-full form-control"
                    v-model="value1"
                    type="date"
                    placeholder="Pick a day"
                    :disabled-date="disabledDate"
                    @change="chooseTimeSlot(value1)"
                    format="YYYY-MM-DD"
                    value-format="YYYY-MM-DD"
                    style="width:100%;"
                    :disabled="!form.warehouse_id"
                />
            </div>
            <div class="mt-4">
                <InputLabel for="time_slot" value="Available Time"/>
                <select
                    id="time_slot"
                    v-model="form.time_slot"
                    class="mt-1 block w-full form-control"
                    required
                >
                    <option disabled value="">Select Time</option>
                    <!-- 遍历所有时间段，并禁用已预约的时间段 -->
                    <option
                        v-for="slot in availableTimeSlots"
                        :key="slot"
                        :value="slot"
                        :disabled="isBooked(slot)"
                        :class="{ 'booked': isBooked(slot) }"
                    >
                        {{ showSlotDate(slot) }}
                    </option>
                </select>
                <InputError class="mt-2" :message="form.errors.time_slot"/>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-center mt-4">
                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Submit
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

<style scoped>
/* 已预约时间段显示为灰色并禁用 */
.booked {
    color: grey;
    cursor: not-allowed;
}

#time_slot {
    border-radius: 5px;
}
</style>
