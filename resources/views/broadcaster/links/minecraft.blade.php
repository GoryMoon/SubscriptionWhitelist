<p>
    These links will try to get to UUID of the usernames in the whitelist, invalid names will be filtered out
</p>
<hr>
<div class="form-group">
    <label for="csv_link">CSV link (comma separated list)</label>
    <input class="form-control selectable" id="csv_link" readonly type="text" value="{{ $base_url }}minecraft_csv">
</div>
<div>
    <label for="csv_example">CSV example</label>
    <textarea id="csv_example" class="form-control" cols="30" rows="1" readonly>
htf3ig9xs8dvlguwzd51x7ipdhcbk2aj,n0emyn14t30cwzmykdwo92cjg7d2wzlk,vf0l5c53cd7k701jeu18oyhp8bqpt7zg</textarea>
</div>
<hr>
<div class="form-group">
    <label for="nl_link">Newline link (newline separated list)</label>
    <input class="form-control selectable" id="nl_link" readonly type="text" value="{{ $base_url }}minecraft_nl">
</div>
<div>
    <label for="nl_example">Newline example</label>
    <textarea id="nl_example" class="form-control" cols="30" rows="3" readonly>
htf3ig9xs8dvlguwzd51x7ipdhcbk2aj
n0emyn14t30cwzmykdwo92cjg7d2wzlk
vf0l5c53cd7k701jeu18oyhp8bqpt7zg</textarea>
</div>
<hr>
<div class="form-group">
    <label for="json_array_link">JSON array link</label>
    <input class="form-control selectable" id="json_array_link" readonly type="text" value="{{ $base_url }}minecraft_json_array">
</div>
<div class="form-group">
    <label for="json_array_example">JSON array example</label>
    <textarea id="json_array_example" class="form-control" cols="30" rows="5" readonly>
[
    "htf3ig9xs8dvlguwzd51x7ipdhcbk2aj",
    "n0emyn14t30cwzmykdwo92cjg7d2wzlk",
    "vf0l5c53cd7k701jeu18oyhp8bqpt7zg"
]</textarea>
</div>
<hr>
<div class="form-group">
    <label for="mw_link">JSON array link</label>
    <input class="form-control selectable" id="mw_link" readonly type="text" value="{{ $base_url }}minecraft_whitelist">
</div>
<div class="form-group">
    <label for="mw_example">JSON array example</label>
    <textarea id="mw_example" class="form-control" cols="30" rows="10" readonly>
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