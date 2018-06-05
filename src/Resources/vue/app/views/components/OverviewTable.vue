<template>

    <table class="table table-bordered table-striped table-overview table-sm">
        <thead>
        <tr>
            <th>
                {{ labels.overviewDomain }}
            </th>
            <template v-for="locale in overviewData.locales">
                <th>
                    {{ locale }}
                </th>
            </template>
        </tr>
        </thead>
        <tbody v-for="(stats, domain) in overviewData.stats">
        <tr>
            <td>{{ domain }}</td>
            <template v-for="(stat, locale) in stats">
                <td class="text-center">
                    <span class="text" :class="stat.completed == 100 ? 'text-success' : 'text-danger'">
                       {{ stat.translated }} / {{ stat.keys }}
                    </span>
                    <div class="progress">
                        <div class="progress-bar"
                             :class="stat.completed == 100 ? 'progress-bar-success' : 'progress-bar-danger'"
                             role="progressbar"
                             :aria-valuenow="stat.completed"
                             aria-valuemin="0"
                             :aria-valuemax="100"
                             :style="{width: stat.completed + '%'}">
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