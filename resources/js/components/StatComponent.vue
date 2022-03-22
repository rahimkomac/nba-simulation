<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <ul v-if="errors && errors.length">
                    <li v-for="(error, index) of errors" :key="index">
                        {{ index + 1 }} - {{ error.message }}
                    </li>
                </ul>
                <div class="card">
                    <div class="card-header">
                        <div class="float-right">
                            <v-btn
                                class="ma-2"
                                :loading="loading"
                                :disabled="loading"
                                color="success"
                                @click="play"
                            >
                                {{ fixtureButtonText }} {{ fixtureSchedule }}
                                <template v-slot:loader>
                                    <span>Playing... {{ fixtureSchedule }}</span>
                                </template>
                            </v-btn>
                        </div>
                    </div>
                    <div class="card-body">
                        <v-container>
                            <v-row class="text-center">
                                <v-col cols="12">
                                    <v-data-table
                                        :headers="headersParent"
                                        :items="matches"
                                        :expanded.sync="expanded"
                                        item-key="fixtureId"
                                        class="elevation-1"
                                        hide-default-footer
                                        show-expand
                                        @click:row="(item, slot) => slot.expand(!slot.isExpanded)"
                                    >
                                        <template #expanded-item="{ headers, item }">
                                            <td class="px-0 py-2" :colspan="headers.length">
                                                <div class="row">
                                                    <v-col cols="6">
                                                        <v-data-table
                                                            :headers="headersChild"
                                                            :items="item.players.homeTeam"
                                                            item-key="id"
                                                            elevation="0"
                                                            hide-default-footer
                                                        >
                                                            <template #[`item.name`]="{ item }">
                                                                <span v-if="item.status === 0" class="text-subtitle-2">{{item.name}}</span>
                                                                <span v-if="item.status === 1" class="text-subtitle-2 green--text">{{item.name}}</span>
                                                                <span v-if="item.status === -1" class="text-subtitle-2 red--text">{{item.name}}</span>
                                                            </template>
                                                        </v-data-table>
                                                    </v-col>
                                                    <v-col cols="6">
                                                        <v-data-table
                                                            :headers="headersChild"
                                                            :items="item.players.awayTeam"
                                                            item-key="id"
                                                            elevation="0"
                                                            hide-default-footer
                                                        >
                                                            <template #[`item.name`]="{ item }">
                                                                <span v-if="item.status === 0" class="text-subtitle-2">{{item.name}}</span>
                                                                <span v-if="item.status === 1" class="text-subtitle-2 green--text">{{item.name}}</span>
                                                                <span v-if="item.status === -1" class="text-subtitle-2 red--text">{{item.name}}</span>
                                                            </template>
                                                        </v-data-table>
                                                    </v-col>
                                                </div>
                                            </td>
                                        </template>
                                    </v-data-table>
                                </v-col>
                            </v-row>
                        </v-container>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        matchData : {
            type: Array,
            default: function () {
                return []
            }
        },
        schedule : {
            type: String,
        },
        playing : {
            type: Boolean,
            default: function () {
                return false
            }
        },
        url : {
            type: String,
        }
    },
    mounted() {
        window.Echo.channel('match-channel').listen('MatchEvent', match => {
            if(match.state == 'finished') {
                this.finishedMatchCount++;
                if(this.matches.length == this.finishedMatchCount) {
                    this.loading = false;
                    this.fixtureButtonText = 'Next Schedule';
                }
            }

            if(this.matches.length > 0) {
                let currentMatch = this.matches.find(m => m.fixtureId === match.fixtureId)
                if(currentMatch) {
                    let index = this.matches.findIndex(m => m.fixtureId === match.fixtureId)
                    this.matches.splice(index, 1, match)
                } else {
                    this.matches.push(match);
                }
            } else {
                this.matches.push(match);
            }
        });

        if(!this.playaing) {
            this.fixtureButtonText = 'Play Schedule';
        }
    },
    methods: {
        async play() {
            try {
                this.loading = true;
                this.fixtureState = 'playing';
                const res = await axios.get(this.url);
                this.fixtureSchedule = res.data.schedule;
                this.matches = res.data.matchData;
            } catch (err) {
                this.errors.push(err);
            }
        },
    },
    data() {
        return {
            finishedMatchCount: 0,
            fixtureState: 'waiting',
            fixtureButtonText: 'Next Week',
            fixtureSchedule: this.schedule,
            loader: null,
            loading: this.playing,
            errors : [],
            expanded: [],
            headersParent: [
                {
                    text: 'Home Team',
                    align: 'left',
                    value: 'homeTeam'
                },
                {
                    text: '',
                    align: 'right',
                    value: 'homePoints'
                },
                {
                    text: ' ',
                    align: 'center',
                    value: 'dash'
                },
                {
                    text: '',
                    align: 'left',
                    value: 'awayPoints'
                },
                {
                    text: 'Away Team',
                    align: 'right',
                    value: 'awayTeam'
                },

            ],
            headersChild: [
                {
                    text: 'Name',
                    align: 'left',
                    value: 'name'
                },
                { text: 'Points', value: 'points' },
                { text: 'Assists', value: 'assist' }
            ],
            matches: this.matchData
        }
    }
}
</script>
