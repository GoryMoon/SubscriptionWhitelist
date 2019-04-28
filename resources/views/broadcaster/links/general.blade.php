<div class="form-group">
    <label for="csv_link">CSV link (comma separated list)</label>
    <input class="form-control selectable" id="csv_link" readonly type="text" value="{{ $base_url }}csv">
</div>
<div>
    <label for="csv_example">CSV example</label>
    <textarea id="csv_example" class="form-control" cols="30" rows="1" readonly>name1,name2,name3,name4,name5</textarea>
</div>
<hr>
<div class="form-group">
    <label for="nl_link">Newline link (newline separated list)</label>
    <input class="form-control selectable" id="nl_link" readonly type="text" value="{{ $base_url }}nl">
</div>
<div>
    <label for="nl_example">Newline example</label>
    <textarea id="nl_example" class="form-control" cols="30" rows="5" readonly>name1
name2
name3
name4
name5</textarea>
</div>
<hr>
<div class="form-group">
    <label for="nl_link">JSON array link</label>
    <input class="form-control selectable" id="nl_link" readonly type="text" value="{{ $base_url }}json_array">
</div>
<div class="form-group">
    <label for="nl_example">JSON array example</label>
    <textarea id="nl_example" class="form-control" cols="30" rows="7" readonly>
[
    "name1",
    "name2",
    "name3",
    "name4",
    "name5"
]</textarea>
</div>