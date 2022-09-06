<template>
    <app-layout>
        <PendingRefund :data="data">
            <template #header>
                <Alert
                    :title="title"
                    :flash="flash"
                    :errors="errors"
                    :link="link"
                    :label="label"
                    :pending="1"
                />
            </template>
        </PendingRefund>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import Models from './components/Models'
import PendingRefund from './components/PendingRefund'
import Alert from '@/Pages/Component/Alert'

export default {
    components: {
        AppLayout,
        Models,
        PendingRefund,
        Alert
    },
    props: ['data', 'errors', 'flash'],
    data () {
        return {
            title: 'Pending Refund Bill List',
            link: 'ipdBillings.create',
            label: 'Create New Bill'
        }
    }
}
</script>
