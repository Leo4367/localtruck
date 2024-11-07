<script setup>
import {ref, defineProps, defineEmits, watch, onMounted} from "vue";
import axios from 'axios';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    }
});
const emit = defineEmits(['update:modelValue']);

const warehouse_id = ref(props.modelValue);

const options = ref([]);

//获取仓库列表
const warehouseList = () => {
    axios.get(route('appointment.warehouse'))
        .then(response => {
            options.value = response.data;
        }).catch(
        error => {
            console.error('Please set warehouse first', error);
        });
}

watch(warehouse_id, (newVal) => {
    emit('update:modelValue', newVal);
});
//组件挂载时获取仓库列表
onMounted(
    () => {
        warehouseList();
    }
);
</script>

<template>
    <el-select
        v-model="warehouse_id"
        placeholder="Select Warehouse"
        size="large"
    >
        <el-option
            v-for="(item,index) in options"
            :key="index"
            :label="item.name"
            :value="item.id"
        >
            <!--添加仓库选项的地址描述-->
            <template #default>
                <el-tooltip :content="`Addr: ${item.address}`" placement="right">
                    <span>{{ item.name }}</span>
                </el-tooltip>
            </template>
        </el-option>
    </el-select>
</template>
