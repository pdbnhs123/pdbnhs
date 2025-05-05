<?php
include 

?><!-- Step 2: Address Information -->
<div id="step2" <?= $current_step !== 2 ? 'class="hidden"' : '' ?>>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="house_no" class="required">House No.</label>
                        <input type="text" name="house_no" value="<?= htmlspecialchars($entered_values['house_no']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="street" class="required">Street</label>
                        <input type="text" name="street" value="<?= htmlspecialchars($entered_values['street']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="barangay" class="required">Barangay</label>
                        <input type="text" name="barangay" value="<?= htmlspecialchars($entered_values['barangay']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="municipality_city" class="required">Municipality/City</label>
                        <input type="text" name="municipality_city" value="<?= htmlspecialchars($entered_values['municipality_city']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="province" class="required">Province</label>
                        <input type="text" name="province" value="<?= htmlspecialchars($entered_values['province']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="country" class="required">Country</label>
                        <input type="text" name="country" value="<?= htmlspecialchars($entered_values['country']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="zip_code" class="required">Zip Code</label>
                        <input type="text" name="zip_code" pattern="\d{4}" value="<?= htmlspecialchars($entered_values['zip_code']) ?>" required>
                    </div>
                </div>
                <div class="form-group full-width">
                    <input type="hidden" name="step" value="2">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <button type="button" class="btn btn-prev" onclick="window.location.href='?step=1'">Previous</button>
                    <button type="submit" class="btn btn-next">Submit Registration</button>
                </div>
            </div>
        </form>
    </div>