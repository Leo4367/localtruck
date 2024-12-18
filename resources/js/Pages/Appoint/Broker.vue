<script setup>
import {Head, useForm} from "@inertiajs/vue3";
import {ElMessage} from "element-plus";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";


const form = useForm({
    customer_name: '',
    deliver_address: '',
    work_order: '',
    email_date: '',
});


const submit = () => {
    if (!form.customer_name) {
        form.errors.customer_name = "Customer Name is required"; // 设置错误信息
        return; // 阻止提交
    }
    form.post(route('inquiry.store'), {
        onSuccess: () => {
            ElMessage({
                message: 'successfully',
                type: 'success',
            });
        },
        onError: (errors) => {
            // 获取并显示详细的错误信息
            if (errors.response && errors.response.data && errors.response.data.errors) {
                const errorMessages = Object.values(errors.response.data.errors)
                    .map(errorArray => errorArray.join(', '))  // 每个字段的错误信息可能是数组，转换为字符串
                    .join('\n');  // 将多个错误信息连接起来，以换行符分隔

                ElMessage.error(`Error :\n${errorMessages}`);
            } else {
                ElMessage.error('Error'); // 默认错误信息
            }
        },
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Broker"/>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight" style="text-align: center">Broker Inquiry</h2>
        </template>
        <el-row :gutter="20">
            <el-col :span="12" :offset="6">
                <el-form @submit.prevent="submit">
                    <div class="mt-4">
                        <InputLabel for="customer_name" value="Customer Name"/>
                        <el-input
                            id="customer_name"
                            class="mt-1 block w-full"
                            type="textarea"
                            v-model="form.customer_name"
                            autosize
                            placeholder="Only one valid Name can be entered in a row"
                        />
                        <InputError class="mt-2" :message="form.errors.customer_name"/>
                    </div>

                    <!--Deliver Address input -->
                    <div class="mt-4">
                        <InputLabel for="deliver_address" value="Deliver Address"/>
                        <el-input
                            id="deliver_address"
                            class="mt-1 block w-full"
                            type="textarea"
                            v-model="form.deliver_address"
                            autosize
                            placeholder="Only one valid Address can be entered in a row"
                        />
                        <InputError class="mt-2" :message="form.errors.deliver_address"/>
                    </div>

                    <!--Work Order input -->
                    <div class="mt-4">
                        <InputLabel for="work_order" value="Work Order"/>
                        <el-input
                            id="work_order"
                            class="mt-1 block w-full"
                            type="textarea"
                            v-model="form.work_order"
                            autosize
                            placeholder="Only one valid Order can be entered in a row"
                        />
                        <InputError class="mt-2" :message="form.errors.work_order"/>
                    </div>

                    <!--Email Date input-->
                    <div class="mt-4">
                        <InputLabel for="email_date" value="Email Date"/>
                        <el-date-picker
                            class="mt-1 block w-full form-control"
                            v-model=form.email_date
                            type="date"
                            placeholder="pick a date"
                            format="YYYY-MM-DD"
                            value-format="YYYY-MM-DD"
                            style="width:100%;"
                            :editable="false"
                        />
                        <InputError class="mt-2" :message="form.errors.email_date"/>
                    </div>

                    <div class="flex items-center justify-center mt-4">
                        <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }"
                                       :disabled="form.processing">
                            Send Email
                        </PrimaryButton>
                    </div>

                </el-form>
            </el-col>
        </el-row>

    </AuthenticatedLayout>
</template>

<style scoped>

</style>
