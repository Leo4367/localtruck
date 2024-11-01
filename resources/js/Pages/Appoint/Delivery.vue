<script setup>
import TextInput from "@/Components/TextInput.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import {Head, useForm,usePage} from "@inertiajs/vue3";
import {onMounted, ref} from 'vue';
import axios from 'axios';
import {format} from 'date-fns';
import SelectOption from "@/Components/SelectOption.vue";
import {ElMessage} from "element-plus";  // 用于格式化日期

const {props} = usePage();

const form = useForm({
    pickup_number: '',
    phone_number: '',
    driver_name: '',
    warehouse_id: '',
    time_slot: '',
    type: 'Delivery',
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

const showSlotDate = (t) => {
    return format(t, 'HH:mm');
};

const chooseTimeSlot = (t) => {
    if (t !== null) {
        form.time_slot = '';
        try {
            //从后端获取可预约时间
            axios.get(route('appointment.booked-slots'),
                {params: {type: 'Delivery', slot: t, warehouse_id: form.warehouse_id}}
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
    axios.get(route('appointment.forbidden-dates'), {params: {type: 'Delivery', warehouse_id: house}})
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
            })
        },
        onError: (errors) => {
            // 获取并显示详细的错误信息
            if (errors.response && errors.response.data && errors.response.data.errors) {
                const errorMessages = Object.values(errors.response.data.errors)
                    .map(errorArray => errorArray.join(', '))  // 每个字段的错误信息可能是数组，转换为字符串
                    .join('\n');  // 将多个错误信息连接起来，以换行符分隔

                ElMessage.error(`Error creating appointment:\n${errorMessages}`);
            } else {
                ElMessage.error('Error creating appointment');  // 默认错误信息
            }
        },
    });
};

</script>

<template>
    <GuestLayout>
        <Head title="Delivery"/>

        <form @submit.prevent="submit">
            <!-- Name Input -->
            <div>
                <InputLabel for="driver_name" value="Name"/>
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
                <InputLabel for="pickup_number" value="Container Number"/>
                <TextInput
                    id="pickup_number"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.pickup_number"
                    required
                    autofocus
                />
                <InputError class="mt-2" :message="form.errors.pickup_number"/>
            </div>

            <div class="mt-4">
                <InputLabel for="warehouse_id" value="Warehouse"/>
                <SelectOption v-model="form.warehouse_id" @change="getForbiddenDates($event)"/>
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
</style>
