<template>
    <app-layout>
        <Models :param="param">
            <template #header>
                <Alert
                    :title="title"
                    :flash="flash"
                    :errors="errors"
                    :link="link"
                    :label="label"
                />
            </template>
        </Models>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import Models from './components/Models'
import Alert from '@/Pages/Component/Alert'

export default {
    components: {
        AppLayout,
        Models,
        Alert
    },
    props: ['param', 'errors', 'flash'],
    data () {
        return {
            title: 'Role List',
            link: 'roles.create',
            label: 'Create Role'
        }
    }
}
</script>
