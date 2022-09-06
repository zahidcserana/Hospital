<template>
    <app-layout>
        <Model :param="param">
            <template #header>
                <Alert
                    :title="title"
                    :flash="flash"
                    :errors="errors"
                    :link="link"
                    :label="label"
                />
            </template>
        </Model>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import Model from './components/Model'
import Alert from '@/Pages/Component/Alert'
export default {
    components: {
        AppLayout,
        Model,
        Alert
    },
    props: ['param', 'errors', 'flash'],
    data () {
        return {
            title: 'Create Requisition',
            link: 'requisitions.index',
            label: 'Requisition List'
        }
    },
    created () {
        if (this.param.type == 2) {
            this.link = 'dept_requisition_create'
        }
    }
}
</script>
