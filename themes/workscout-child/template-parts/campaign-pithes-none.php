<div class="table">
    <div class="table__head">
        <div class="table__row table__row_header">
            <div class="table__header">
                <p class="table__text">Campaign</p>
            </div>
            <div class="table__header">
                <p class="table__text">Location</p>
            </div>
            <div class="table__header">
                <p class="table__text">Campaign Date</p>
            </div>
            <div class="table__header">
                <p class="table__text">Campaign Description</p>
            </div>
            <div class="table__header">
                <p class="table__text"># of Pitches</p>
            </div>
        </div>
    </div>
    <div class="table__body">
        <div class="table__row table__row_body">
             <p class="single_text">You haven't any Pitches with <?php echo _n('status ', 'statuses ', count($statuses)) ;?><?php  foreach ($statuses as $status ) echo $status; ?> </p>
        </div>
    </div>
</div>