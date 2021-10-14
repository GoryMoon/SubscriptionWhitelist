<p>
    These links will try to get the UUID of the usernames in the whitelist, invalid names will be filtered out
</p>
<hr>
<h4>Verified Names</h4>
<div class="form-group">
    <label for="mc_csv_link">CSV link (comma separated list)</label>
    <copy-link-component id="mc_csv_link" link="{{ $base_url }}minecraft_csv"></copy-link-component>
</div>
<div>
    <label for="mc_csv_example">CSV example</label>
    <textarea id="mc_csv_example" class="form-control text-monospace" cols="30" rows="1" readonly>
name1,name2,name3</textarea>
</div>
<hr>
<div class="form-group">
    <label for="mc_nl_link">Newline link (newline separated list)</label>
    <copy-link-component id="mc_nl_link" link="{{ $base_url }}minecraft_nl"></copy-link-component>
</div>
<div>
    <label for="mc_nl_example">Newline example</label>
    <textarea id="mc_nl_example" class="form-control text-monospace" cols="30" rows="3" readonly>
name1
name2
name3</textarea>
</div>
<hr>
<div class="form-group">
    <label for="mc_nl_name_link">Newline with Twitch usernames link (newline separated list)</label>
    <copy-link-component id="mc_nl_name_link" link="{{ $base_url }}minecraft_twitch_nl"></copy-link-component>
</div>
<div>
    <label for="mc_nl_name_example">Newline with Twitch example</label>
    <textarea id="mc_nl_name_example" class="form-control text-monospace" cols="30" rows="3" readonly>
name1:twitch1
name2:alternateName
name3</textarea>
</div>
<hr>
<div class="form-group">
    <label for="mc_json_array_link">JSON array link</label>
    <copy-link-component id="mc_json_array_link" link="{{ $base_url }}minecraft_json_array"></copy-link-component>
</div>
<div class="form-group">
    <label for="mc_json_array_example">JSON array example</label>
    <textarea id="mc_json_array_example" class="form-control text-monospace" cols="30" rows="5" readonly>
[
    "name1",
    "name2",
    "name3"
]</textarea>
</div>
<hr>
<h4>Verifed UUIDs</h4>
<div class="form-group">
    <label for="mc_uuid_csv_link">CSV link (comma separated list)</label>
    <copy-link-component id="mc_uuid_csv_link" link="{{ $base_url }}minecraft_uuid_csv"></copy-link-component>
</div>
<div>
    <label for="mc_uuid_csv_example">CSV example</label>
    <textarea id="mc_uuid_csv_example" class="form-control text-monospace" cols="30" rows="1" readonly>
htf3ig9xs8dvlguwzd51x7ipdhcbk2aj,n0emyn14t30cwzmykdwo92cjg7d2wzlk,vf0l5c53cd7k701jeu18oyhp8bqpt7zg</textarea>
</div>
<hr>
<div class="form-group">
    <label for="mc_uuid_nl_link">Newline link (newline separated list)</label>
    <copy-link-component id="mc_uuid_nl_link" link="{{ $base_url }}minecraft_uuid_nl"></copy-link-component>
</div>
<div>
    <label for="mc_uuid_nl_example">Newline example</label>
    <textarea id="mc_uuid_nl_example" class="form-control text-monospace" cols="30" rows="3" readonly>
htf3ig9xs8dvlguwzd51x7ipdhcbk2aj
n0emyn14t30cwzmykdwo92cjg7d2wzlk
vf0l5c53cd7k701jeu18oyhp8bqpt7zg</textarea>
</div>
<hr>
<div class="form-group">
    <label for="mc_uuid_json_array_link">JSON array link</label>
    <copy-link-component id="mc_uuid_json_array_link" link="{{ $base_url }}minecraft_uuid_json_array"></copy-link-component>
</div>
<div class="form-group">
    <label for="mc_uuid_json_array_example">JSON array example</label>
    <textarea id="mc_uuid_json_array_example" class="form-control text-monospace" cols="30" rows="5" readonly>
[
    "htf3ig9xs8dvlguwzd51x7ipdhcbk2aj",
    "n0emyn14t30cwzmykdwo92cjg7d2wzlk",
    "vf0l5c53cd7k701jeu18oyhp8bqpt7zg"
]</textarea>
</div>
<hr>
<h4>Verified whitelist</h4>
<p>
    This is the structure of the <code>whitelist.json</code> that minecraft servers can use
</p>
<div class="form-group">
    <label for="mw_link">JSON whitelist link</label>
    <copy-link-component id="mw_link" link="{{ $base_url }}minecraft_whitelist"></copy-link-component>
</div>
<div class="form-group">
    <label for="mw_example">JSON whitelist example</label>
    <textarea id="mw_example" class="form-control text-monospace" cols="30" rows="10" readonly>
[
    {
         "uuid": "htf3ig9xs8dvlguwzd51x7ipdhcbk2aj",
         "name": "user1"
    },
    {
         "uuid": "n0emyn14t30cwzmykdwo92cjg7d2wzlk",
         "name": "user2"
    }
]</textarea>
</div>
