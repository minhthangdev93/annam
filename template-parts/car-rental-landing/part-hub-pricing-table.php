<?php
/**
 * Bảng giá hub — desktop table + mobile tabs.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$vehicle_types = annam_car_rental_get_vehicle_types();
$routes        = annam_car_rental_get_routes_raw();
$type_keys     = array_keys( $vehicle_types );

$tab_labels = array(
	'7-cho'          => '7 chỗ',
	'limousine-9-11' => 'Limo 9–11',
	'16-cho'         => '16 chỗ',
	'29-cho'         => '29 chỗ',
	'45-cho'         => '45 chỗ',
);
?>
<div class="annam-cr-hub-pricing" data-annam-cr-hub-pricing>
	<div class="annam-cr-hub-pricing__mobile" aria-label="<?php esc_attr_e( 'Bảng giá theo loại xe', 'generatepress_child' ); ?>">
		<div class="annam-cr-hub-pricing__tabs-wrap">
			<div class="annam-cr-hub-pricing__tabs" role="tablist" aria-label="<?php esc_attr_e( 'Chọn loại xe', 'generatepress_child' ); ?>">
				<?php foreach ( $type_keys as $ti => $vkey ) : ?>
					<?php
					$tab_label = $tab_labels[ $vkey ] ?? ( $vehicle_types[ $vkey ]['label'] ?? $vkey );
					$is_first  = 0 === (int) $ti;
					?>
					<button
						type="button"
						class="annam-cr-hub-pricing__tab<?php echo $is_first ? ' is-active' : ''; ?>"
						role="tab"
						id="annam-cr-hub-tab-<?php echo esc_attr( $vkey ); ?>"
						aria-selected="<?php echo $is_first ? 'true' : 'false'; ?>"
						aria-controls="annam-cr-hub-panel-<?php echo esc_attr( $vkey ); ?>"
						data-annam-cr-hub-tab="<?php echo esc_attr( $vkey ); ?>"
					>
						<?php echo esc_html( $tab_label ); ?>
					</button>
				<?php endforeach; ?>
			</div>
		</div>

		<?php foreach ( $type_keys as $ti => $vkey ) : ?>
			<?php
			$type_label = $vehicle_types[ $vkey ]['label'] ?? '';
			$is_first   = 0 === (int) $ti;
			?>
			<div
				class="annam-cr-hub-pricing__panel"
				role="tabpanel"
				id="annam-cr-hub-panel-<?php echo esc_attr( $vkey ); ?>"
				aria-labelledby="annam-cr-hub-tab-<?php echo esc_attr( $vkey ); ?>"
				data-annam-cr-hub-panel="<?php echo esc_attr( $vkey ); ?>"
				<?php echo $is_first ? '' : ' hidden'; ?>
			>
				<p class="annam-cr-hub-pricing__panel-desc">
					<?php
					printf(
						/* translators: %s: vehicle label */
						esc_html__( 'Giá %s — 2 chiều, xuất phát từ Hà Nội', 'generatepress_child' ),
						esc_html( $type_label )
					);
					?>
				</p>
				<ul class="annam-cr-hub-price-list">
					<?php foreach ( $routes as $route ) : ?>
						<?php
						$price = isset( $route['prices'][ $vkey ] ) ? (int) $route['prices'][ $vkey ] : 0;
						if ( $price <= 0 ) {
							continue;
						}
						$label_display = annam_car_rental_format_route_pricing_label( $route['label'] );
						?>
						<li class="annam-cr-hub-price-list__item<?php echo ! empty( $route['hot'] ) ? ' annam-cr-hub-price-list__item--hot' : ''; ?>">
							<span class="annam-cr-hub-price-list__route">
								<?php if ( ! empty( $route['hot'] ) ) : ?>
									<span class="annam-cr-hub-price-list__hot" aria-hidden="true">🔥</span>
								<?php endif; ?>
								<?php echo esc_html( $label_display ); ?>
							</span>
							<strong class="annam-cr-hub-price-list__price"><?php echo esc_html( annam_car_rental_format_price( $price ) ); ?></strong>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endforeach; ?>
	</div>

	<div class="annam-cr-hub-pricing__desktop">
		<div class="annam-cr-table-wrap annam-cr-table-wrap--scroll annam-cr-table-wrap--sticky-head">
			<div class="annam-cr-table-wrap__x-scroll">
				<table class="annam-cr-table annam-cr-table--hub">
					<thead>
						<tr>
							<th scope="col"><?php esc_html_e( 'Hành trình', 'generatepress_child' ); ?></th>
							<?php foreach ( $vehicle_types as $type ) : ?>
								<th scope="col"><?php echo esc_html( $type['label'] ); ?></th>
							<?php endforeach; ?>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $routes as $route ) : ?>
							<tr>
								<td>
									<?php if ( ! empty( $route['hot'] ) ) : ?><span class="annam-cr-hot">🔥</span><?php endif; ?>
									<?php echo esc_html( annam_car_rental_format_route_pricing_label( $route['label'] ) ); ?>
								</td>
								<?php foreach ( $type_keys as $vkey ) : ?>
									<td class="annam-cr-table__price">
										<?php
										$p = isset( $route['prices'][ $vkey ] ) ? (int) $route['prices'][ $vkey ] : 0;
										echo esc_html( annam_car_rental_format_price( $p ) );
										?>
									</td>
								<?php endforeach; ?>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
