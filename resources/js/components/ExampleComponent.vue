<template>
    <v-container>
        <v-row class="text-center">
            <v-col cols="12">
                <v-data-table
                    :headers="headersParent"
                    :items="treats"
                    :expanded.sync="expanded"
                    show-select
                    item-key="category"
                    class="elevation-1"
                    hide-default-header
                    hide-default-footer
                    @click:row="(item, slot) => slot.expand(!slot.isExpanded)"
                >
                    <template #[`item.category`]="{ item }">
                        <span class="text-subtitle-2 blue--text text--darken-3">{{item.category}}</span>
                    </template>

                    <template #expanded-item="{ headers, item }">
                        <td class="px-0 py-2" :colspan="headers.length">
                            <v-data-table
                                :headers="headersChild"
                                :items="item.food"
                                item-key="name"
                                elevation="0"
                                show-select
                                hide-default-footer
                            >
                                <template #[`item.name`]="{ item }">
                                    <span class="text-subtitle-2 purple--text text--darken-3">{{item.name}}</span>
                                </template>
                            </v-data-table>
                        </td>
                    </template>
                </v-data-table>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
export default {
    name: 'HelloWorld',
    data: () => ({
        selectedParent: [],
        selectedChild: [],
        expanded: [],
        headersParent: [
            {
                text: 'Select',
                align: 'left',
                value: 'data-table-select',
                class: 'checkbox',
                cellClass: 'checkbox'
            },
            {
                text: 'Category',
                align: 'left',
                value: 'category'
            }],
        headersChild: [
            {
                text: 'Name',
                align: 'left',
                value: 'name'
            },
            { text: 'Calories', value: 'calories' },
            { text: 'Fat (g)', value: 'fat' },
            { text: 'Carbs (g)', value: 'carbs' },
            { text: 'Protein (g)', value: 'protein' },
            { text: 'Iron (%)', value: 'iron' }
        ],
        treats: [
            {
                category: 'Desserts',
                food: [
                    {
                        name: 'Frozen Yogurt',
                        calories: 159,
                        fat: 6.0,
                        carbs: 24,
                        protein: 4.0,
                        iron: '1%'
                    },
                    {
                        name: 'Ice cream sandwich',
                        calories: 237,
                        fat: 9.0,
                        carbs: 37,
                        protein: 4.3,
                        iron: '1%'
                    },
                    {
                        name: 'Cupcake',
                        calories: 305,
                        fat: 3.7,
                        carbs: 67,
                        protein: 4.3,
                        iron: '8%'
                    }
                ]
            },
            {
                category: 'Entries',
                food: [
                    {
                        name: 'Melon',
                        calories: 159,
                        fat: 6.0,
                        carbs: 24,
                        protein: 4.0,
                        iron: '1%'
                    },
                    {
                        name: 'Hummus',
                        calories: 237,
                        fat: 9.0,
                        carbs: 37,
                        protein: 4.3,
                        iron: '1%'
                    }
                ]
            }
        ]
    }),
    watch: {
        selectedParent(val) {
            // When a parent get's selected, select all his childs
            // Unless partial childs are already selected
            this.selectedChild = []
            if (val.length > 0) {
                val.forEach(category => {
                    category.food.forEach(foodItem => {
                        this.selectedChild.push(foodItem)
                    })
                })
            }
        }
    }
}
</script>

<style>
.checkbox {
    width: 25px;
}
</style>
