<template>
    <app-layout>
        <Model :data="data">
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
            title: 'Create New Bill',
            link: 'ipdBillings.index',
            label: 'Bill List'
        }
    }
}
</script>
