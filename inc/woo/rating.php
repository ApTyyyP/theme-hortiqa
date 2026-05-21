<?php


add_filter('woocommerce_product_get_rating_html', 'custom_rating', 10, 3);

function custom_rating($html, $rating, $count)
{
  if ($rating <= 0) return '';

  $rating_int = floor($rating);

  ob_start();
?>
  <div class="rating" data-rate-total="<?php echo esc_attr($rating); ?>">
    <?php for ($i = 5; $i >= 1; $i--) : ?>
      <svg
        class="rating__star <?php echo ($i <= $rating_int) ? 'active' : ''; ?>"
        data-rate="<?php echo $i; ?>"
        viewBox="0 0 26 25">
        <path d="M11.5204 1.9421C11.986 0.508953 14.0135 0.508955 14.4792 1.94211L16.2677 7.44656C16.476 8.08748 17.0732 8.52142 17.7471 8.52142H23.5349C25.0418 8.52142 25.6683 10.4497 24.4492 11.3354L19.7668 14.7374C19.2216 15.1335 18.9935 15.8356 19.2017 16.4765L20.9902 21.981C21.4559 23.4142 19.8156 24.6059 18.5965 23.7202L13.9141 20.3182C13.3689 19.9221 12.6307 19.9221 12.0855 20.3182L7.40308 23.7202C6.18397 24.6059 4.54367 23.4141 5.00933 21.981L6.79784 16.4765C7.00608 15.8356 6.77795 15.1335 6.23275 14.7374L1.55038 11.3354C0.331269 10.4497 0.95781 8.52142 2.46471 8.52142H8.25243C8.92634 8.52142 9.52361 8.08748 9.73186 7.44656L11.5204 1.9421Z" />
      </svg>
    <?php endfor; ?>

  </div>
<?php
  return ob_get_clean();
}



