<template>

    <table class="table table-bordered table-striped table-overview">
        <thead>
        <tr>
            <th>
                {{ labels.overviewDomain }}
            </th>
            <template v-for="locale in locales">
                <th>
                    {{ locale }}
                </th>
            </template>
        </tr>
        </thead>
        <tbody v-for="domain in overviewData.domains">
        <tr>
            <td>{{ domain }}</td>
            <template v-for="locale in locales">
                <td class="text-center">
                <span class="text" :class="[overviewData.stats[domain][locale].completed == 100 ? 'text-success' : 'text-danger']">
                   {{ overviewData.stats[domain][locale].translated }} / {{ overviewData.stats[domain][locale].keys }}
                </span>
                    <div class="progress">
                        <div class="progress-bar"
                             :class="[overviewData.stats[domain][locale].completed == 100 ? 'progress-bar-success' : 'progress-bar-danger']"
                             role="progressbar"
                             :aria-valuenow="overviewData.stats[domain][locale].completed"
                             aria-valuemin="0"
                             :aria-valuemax="100"
                             :style="{width: overviewData.stats[domain][locale].completed + '%'}">
                        </div>
                    </div>
                </td>
            </template>
        </tr>
        </tbody>
    </table>

</template>

<script>

    export default {
        props: ['locales', 'labels', 'overviewData']
    }

</script>