<template>
    <app-layout>
        <Model :data="data" :editMode="1" :refund="1">
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
    props: ['data', 'errors', 'flash'],
    data () {
        return {
            title: 'Refund From Bill',
            link: 'opdBillings.index',
            label: 'Bill List'
        }
    },
    mounted () {

    }
}
</script>
