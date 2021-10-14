<div class="form-group">
    <label for="csv_link">CSV link (comma separated list)</label>
    <copy-link-component id="csv_link" link="{{ $base_url }}csv"></copy-link-component>
</div>
<div>
    <label for="csv_example">CSV example</label>
    <textarea id="csv_example" class="form-control text-monospace" cols="30" rows="1" readonly>name1,name2,name3,name4,name5</textarea>
</div>
<hr>
<div class="form-group">
    <label for="nl_link">Newline link (newline separated list)</label>
    <copy-link-component id="nl_link" link="{{ $base_url }}nl"></copy-link-component>
</div>
<div>
    <label for="nl_example">Newline example</label>
    <textarea id="nl_example" class="form-control text-monospace" cols="30" rows="5" readonly>name1
name2
name3
name4
name5</textarea>
</div>
<hr>
<div class="form-group">
    <label for="json_array_link">JSON array link</label>
    <copy-link-component id="json_array_link" link="{{ $base_url }}json_array"></copy-link-component>
</div>
<div class="form-group">
    <label for="json_array_example">JSON array example</label>
    <textarea id="json_array_example" class="form-control text-monospace" cols="30" rows="7" readonly>
[
    "name1",
    "name2",
    "name3",
    "name4",
    "name5"
]</textarea>
</div>
