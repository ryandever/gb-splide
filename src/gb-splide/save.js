import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const blockProps = useBlockProps.save();
    const {
        type,
        perPage,
        perPageResponsive,
        perMove,
        gap,
        autoplay,
        interval,
        pauseOnHover,
        speed,
        arrows,
        pagination,
        direction,
        rewind,
        height,
        width,
    } = attributes;

    // Nếu có perPageResponsive (object), cấu hình breakpoints cho Splide
    const options = {
        type,
        perPage: perPageResponsive?.desktop || perPage || 1,
        perMove,
        gap,
        autoplay,
        interval,
        pauseOnHover,
        speed,
        arrows,
        pagination,
        direction,
        rewind,
        height,
        width,
        ...(perPageResponsive && {
            breakpoints: {
                1024: {
                    perPage: perPageResponsive.tablet || perPage || 1,
                },
                768: {
                    perPage: perPageResponsive.mobile || perPage || 1,
                },
            },
        }),
    };

    return (
        <div {...blockProps}>
            <div
                className="splide"
                data-splide={JSON.stringify(options)}
                style={{
                    width: width || undefined,
                    height: height || undefined,
                }}
            >
                <div className="splide__track">
                    <div className="splide__list">
                        <InnerBlocks.Content />
                    </div>
                </div>
            </div>
        </div>
    );
}