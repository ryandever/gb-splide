import { __ } from '@wordpress/i18n';
import {
    InspectorControls,
    useBlockProps,
    InnerBlocks,
    BlockControls
} from '@wordpress/block-editor';
import {
    PanelBody,
    TextControl,
    ToggleControl,
    SelectControl,
    RangeControl,
    ToolbarGroup,
    ToolbarButton
} from '@wordpress/components';
import { Fragment, useEffect } from '@wordpress/element';
import { useDispatch, useSelect } from '@wordpress/data';
import { createBlock } from '@wordpress/blocks';

import './editor.scss';

const SPLIDE_TYPES = [
    { label: 'Slide', value: 'slide' },
    { label: 'Loop', value: 'loop' },
    { label: 'Fade', value: 'fade' }
];

export default function Edit({ attributes, setAttributes, clientId }) {
    const blockProps = useBlockProps();
    const { insertBlocks } = useDispatch('core/block-editor');

    const innerBlocks = useSelect((select) =>
        select('core/block-editor').getBlock(clientId)?.innerBlocks || []
    )

    const generateId = () => {
        return 'splide-' + Math.floor(Math.random() * 100);
    }

    const {
        type,
        perPage,
        perPageResponsive = { desktop: 3, tablet: 2, mobile: 1 },
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
        slides
    } = attributes;

    const newSlide = () => {
        const image = createBlock(
            'generateblocks/image',
            {
                uniqueId: generateId()
            }
        )
        const slide = createBlock(
            'generateblocks/element',
            {
                uniqueId: generateId(),
                isDynamic: true,
                blockVersion: 3,
                tagName: 'div',
                className: 'splide__slide',
                metadata: {
                    name: 'Slide',
                }
            },
            [image]
        );

        return slide;
    }

    const addSlide = () => {
        const slide = newSlide();
        setAttributes({ slides: [...attributes.slides, slide] });
        insertBlocks(slide, attributes.slides.length, clientId);
    }

    const renderBlock = () => {
        if (innerBlocks.length === 0) {
            const _slides = [addSlide(), addSlide(), addSlide()];
            setAttributes({ slides: _slides });
            insertBlocks(_slides, 0, clientId);
        }
    }

    useEffect(() => {
        setTimeout(() => {
            renderBlock();
        }, 100);
    }, []);

    return (
        <Fragment>
            <InspectorControls>
                <PanelBody title={__('Settings', 'generateblocks')} initialOpen={true}>
                    <SelectControl
                        label={__('Type', 'generateblocks')}
                        value={type}
                        options={SPLIDE_TYPES}
                        onChange={(value) => setAttributes({ type: value })}
                    />
                    <RangeControl
                        label={__('Slides per Page', 'generateblocks')}
                        value={perPage}
                        onChange={(value) => {
                            setAttributes({ perPageResponsive: { ...perPageResponsive, desktop: value } });
                            setAttributes({ perPage: value })
                        }}
                        min={1}
                        max={10}
                    />
                    <RangeControl
                        label={__('Slides per Move', 'generateblocks')}
                        value={perMove}
                        onChange={(value) => setAttributes({ perMove: value })}
                        min={1}
                        max={10}
                    />
                    <TextControl
                        label={__('Gap Between Slides', 'generateblocks')}
                        value={gap}
                        onChange={(value) => setAttributes({ gap: value })}
                        help="Example: 1rem, 10px"
                    />
                    <RangeControl
                        label={__('Speed (ms)', 'generateblocks')}
                        value={speed}
                        onChange={(value) => setAttributes({ speed: value })}
                        min={100}
                        max={2000}
                        step={50}
                    />
                    <ToggleControl
                        label={__('Autoplay', 'generateblocks')}
                        checked={autoplay}
                        onChange={(value) => setAttributes({ autoplay: value })}
                    />
                    <RangeControl
                        label={__('Autoplay Interval (ms)', 'generateblocks')}
                        value={interval}
                        onChange={(value) => setAttributes({ interval: value })}
                        min={1000}
                        max={10000}
                        step={500}
                        disabled={!autoplay}
                    />
                    <ToggleControl
                        label={__('Pause on Hover', 'generateblocks')}
                        checked={pauseOnHover}
                        onChange={(value) => setAttributes({ pauseOnHover: value })}
                    />
                    <ToggleControl
                        label={__('Show Arrows', 'generateblocks')}
                        checked={arrows}
                        onChange={(value) => setAttributes({ arrows: value })}
                    />
                    <ToggleControl
                        label={__('Show Pagination', 'generateblocks')}
                        checked={pagination}
                        onChange={(value) => setAttributes({ pagination: value })}
                    />
                    <ToggleControl
                        label={__('Rewind', 'generateblocks')}
                        checked={rewind}
                        onChange={(value) => setAttributes({ rewind: value })}
                    />
                    <SelectControl
                        label={__('Direction', 'generateblocks')}
                        value={direction}
                        options={[
                            { label: 'Left to Right (LTR)', value: 'ltr' },
                            { label: 'Right to Left (RTL)', value: 'rtl' }
                        ]}
                        onChange={(value) => setAttributes({ direction: value })}
                    />
                    <TextControl
                        label={__('Width', 'generateblocks')}
                        value={width}
                        onChange={(value) => setAttributes({ width: value })}
                        help="e.g., 100%, 800px"
                    />
                    <TextControl
                        label={__('Height', 'generateblocks')}
                        value={height}
                        onChange={(value) => setAttributes({ height: value })}
                        help="e.g., auto, 400px"
                    />
                </PanelBody>
                <PanelBody title={__('Responsive', 'generateblocks')} initialOpen={false}>
                    <RangeControl
                        label={__('Desktop (≥1024px)', 'generateblocks')}
                        value={perPageResponsive.desktop}
                        onChange={(value) => {
                            setAttributes({ perPageResponsive: { ...perPageResponsive, desktop: value } });
                            setAttributes({ perPage: value })
                        }}
                        min={1}
                        max={10}
                    />
                    <RangeControl
                        label={__('Tablet (≥768px)', 'generateblocks')}
                        value={perPageResponsive.tablet}
                        onChange={(value) => setAttributes({ perPageResponsive: { ...perPageResponsive, tablet: value } })}
                        min={1}
                        max={10}
                    />
                    <RangeControl
                        label={__('Mobile (<768px)', 'generateblocks')}
                        value={perPageResponsive.mobile}
                        onChange={(value) => setAttributes({ perPageResponsive: { ...perPageResponsive, mobile: value } })}
                        min={1}
                        max={10}
                    />
                </PanelBody>
            </InspectorControls>

            <BlockControls>
                <ToolbarGroup>
                    <ToolbarButton
                        icon="plus"
                        label={__('Add', 'generateblocks')}
                        onClick={addSlide}
                    />
                </ToolbarGroup>
            </BlockControls>

            <div {...blockProps}>
                <div className="splide splide--preview">
                    <p className='splide--preview__txt'>
                        <strong>PREVIEW ONLY</strong> - Splide will render on frontend. <a href="https://minhthe.net">https://minhthe.net</a>
                    </p>
                    <div className="splide__track">
                        <div className="splide__list">
                            <InnerBlocks />
                        </div>
                    </div>
                </div>
            </div>
        </Fragment>
    );
}