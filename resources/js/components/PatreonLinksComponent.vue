<template>
    <div>
        <b-card title="Filters">
            <p>
                Filter users by cent amount or tier
            </p>
            <b-tabs v-model="selectedTab" content-class="mt-3">
                <b-tab title="Pledge amount">
                    <b-form-group>
                        <label for="patreon_cent_min">The minimum pledged amount in cents. (Inclusive)<br/>(0 is default) (Based on the currency used on your Patreon)</label>
                        <b-form-input v-model="centMin" type="number" class="mb-2 mr-sm-2" id="patreon_cent_min" placeholder="0"></b-form-input>
                    </b-form-group>
                    <b-form-group>
                        <label for="patreon_cent_max">The maximum pledged amount in cents. (Inclusive)<br/>(No upper limit as default) (Based on the currency used on your Patreon)</label>
                        <b-form-input v-model="centMax" type="number" class="mb-2 mr-sm-2" id="patreon_cent_max" placeholder="Infinite"></b-form-input>
                    </b-form-group>
                    <b-form-group>
                        <b-form-checkbox v-model="total" class="ml-2" id="patreon_total" :value="true" :disabled="!hasMinOrMax" name="patreon_total" >
                            If the cent amount should be lifetime total a user have pledged. <span v-if="!hasMinOrMax">(Requires either max or min amount)</span>
                        </b-form-checkbox>
                    </b-form-group>
                </b-tab>
                <b-tab title="Tier">
                    <label>Select tier to filter users by.</label>
                    <b-form-group>
                        <b-form-select v-model="tierSelected" :options="options">
                            <template #first>
                                <b-form-select-option :value="null">Please select a tier (No tier)</b-form-select-option>
                            </template>
                        </b-form-select>
                    </b-form-group>
                </b-tab>
            </b-tabs>
            <hr/>
            <b-form-group>
                <b-form-checkbox v-model="payed" class="ml-2" id="patreon_payed_once" :value="true" name="patreon_payed_once" >
                    Require the user to have payed the pledge at least once. (Useful if not paying upfront)
                </b-form-checkbox>
            </b-form-group>
            <b-form-group>
                <b-form-checkbox v-model="former" class="ml-2" id="patreon_former" :disabled="haveTier" :value="true" name="patreon_former" >
                    Include former users in the list. <span v-if="haveTier">(Does not work with tiers)</span>
                </b-form-checkbox>
            </b-form-group>
        </b-card>
        <b-card title="Links" class="mt-3">
            <b-form-group>
                <label for="patreon_csv_link">CSV link (comma separated list)</label>
                <copy-link-component id="patreon_csv_link" :link="link + 'patreon_csv' + query"></copy-link-component>
            </b-form-group>
            <b-form-group>
                <label for="patreon_csv_example">CSV example</label>
                <textarea id="patreon_csv_example" class="form-control text-monospace" cols="30" rows="1" readonly>John Doe,Jane Doe,name1,name2,name3</textarea>
            </b-form-group>
            <hr>
            <b-form-group>
                <label for="patreon_nl_link">Newline link (newline separated list)</label>
                <copy-link-component id="patreon_nl_link" :link="link + 'patreon_nl' + query"></copy-link-component>
            </b-form-group>
            <b-form-group>
                <label for="patreon_nl_example">Newline example</label>
                <textarea id="patreon_nl_example" class="form-control text-monospace" cols="30" rows="5" readonly>John Doe
Jane Doe
name1
name2
name3</textarea>
            </b-form-group>
            <hr>
            <b-form-group>
                <label for="patreon_json_link">JSON array link</label>
                <copy-link-component id="patreon_json_link" :link="link + 'patreon_json_array' + query"></copy-link-component>
            </b-form-group>
            <b-form-group class="form-group">
                <label for="patreon_json_example">JSON array example</label>
                <textarea id="patreon_json_example" class="form-control text-monospace" cols="30" rows="7" readonly>
[
    "John Doe",
    "Jane Doe",
    "name1",
    "name2",
    "name3"
]</textarea>
            </b-form-group>
        </b-card>
    </div>
</template>

<script>
    export default {
        props: {
            link: String,
            tiers: {
                type: Object
            }
        },
        data() {
            return {
                selectedTab: 0,
                tierSelected: null,
                centMin: null,
                centMax: null,
                payed: false,
                total: false,
                former: false,
            }
        },
        computed: {
            hasMinOrMax() {
                return this.centMax > 0 || this.centMin > 0;
            },
            useTiers() {
                return this.selectedTab === 1;
            },
            haveTier() {
              return this.tierSelected != null;
            },
            options() {
                let opt = [];
                for (let tier of this.tiers) {
                    opt.push({ value: tier.id, text: tier.title });
                }
                return opt;
            },
            query() {
                let query = '';
                if (!this.useTiers) {
                    if (this.centMin && this.centMin > 0) {
                        query += 'min=' + this.centMin;
                    }
                    if (this.centMax && this.centMax > 0)
                    {
                        query += (query.length > 0 ? '&' : '') + 'max=' + this.centMax;
                    }
                    if (this.total && (this.centMax > 0 || this.centMin > 0)) {
                        query += (query.length > 0 ? '&': '') + 'to=1'
                    }
                } else {
                    if (this.haveTier) {
                        query += 't=' + this.tierSelected;
                    }
                }
                if (this.payed) {
                    query += (query.length > 0 ? '&': '') + 'py=1'
                }
                if (this.former && !this.haveTier) {
                    query += (query.length > 0 ? '&': '') + 'f=1'
                }
                return query.length > 0 ? '?' + query: '';
            }
        }
    }
</script>

<style scoped>

</style>
