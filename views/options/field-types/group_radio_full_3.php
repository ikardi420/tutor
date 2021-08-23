<div class="tutor-option-field-row d-block">
    <div class="tutor-option-field-label">
        <label><?php echo $field['label']; ?></label>
        <p class="desc"><?php echo $field['desc'] ?></p>
    </div>
    <div class="tutor-option-field-input">
        <div class="radio-thumbnail has-title public-profile fields-wrapper">
            <?php foreach ($field['group_options'] as $key => $option) : ?>
                <label for="profile-<?php echo $key ?>">
                    <input type="radio" name="profile-<?php echo $field['key'] ?>" id="profile-<?php echo $key ?>">
                    <span class="icon-wrapper">
                        <img src="<?php echo tutor()->url ?>assets/images/images-v2/<?php echo $option['image']; ?>" alt="">
                    </span>
                    <span class="title"><?php echo $option['title']; ?></span>
                </label>
            <?php endforeach; ?>
        </div>
    </div>
</div>