<script setup>
import { onMounted, ref, defineProps } from 'vue';
import * as d3 from 'd3';

defineOptions({ layout: BaseLayout });

const props = defineProps({
  barcode: String,
  off_categories: Array,
  agribalyse_categories: Array,
  links_0: Array,
  links_1: Array
});

const svgContainer = ref(null);

onMounted(() => {
  const width = 1500;
  const height = 600;
  const maxStrokeWidth = 5;

  const svg = d3.select(svgContainer.value)
      .attr('width', width)
      .attr('height', height);

  const addTextWithBackground = (x, y, text, anchor = 'middle') => {
    const padding = 2;
    const background = svg.append('rect')
        .attr('fill', 'white')
        .attr('opacity', 0.9);

    const textElement = svg.append('text')
        .attr('x', x)
        .attr('y', y)
        .attr('text-anchor', anchor)
        .text(text);

    const bbox = textElement.node().getBBox();
    background
        .attr('x', bbox.x - padding)
        .attr('y', bbox.y - padding)
        .attr('width', bbox.width + 2 * padding)
        .attr('height', bbox.height + 2 * padding);

    return textElement;
  };

  const addWrappedText = (x, y, text, maxWidth = 200) => {
    const words = text.split(' ');
    let line = '';
    let dy = 0;

    words.forEach((word, i) => {
      const testLine = line + word + ' ';
      const testWidth = getTextWidth(testLine);

      if (testWidth > maxWidth && i > 0) {
        svg.append('text')
            .attr('x', x)
            .attr('y', y + dy)
            .attr('font-size', '12px')
            .attr('dominant-baseline', 'middle')
            .text(line.trim());
        line = word + ' ';
        dy += 20;
      } else {
        line = testLine;
      }
    });

    // Add remaining line
    if (line) {
      svg.append('text')
          .attr('x', x)
          .attr('y', y + dy)
          .attr('font-size', '12px')
          .attr('dominant-baseline', 'middle')
          .text(line.trim());
    }

    return dy;
  };

  const getTextWidth = (text) => {
    return text.length * 6;
  };

  const productX = width * 0.05;
  const offX = width * 0.4;
  const agribalyseX = width * 0.7;

  const offY = d3.scalePoint()
      .domain(props.off_categories.map(d => d.id))
      .range([100, height - 100]);

  const agribalyseY = d3.scalePoint()
      .domain(props.agribalyse_categories.map(d => d.id))
      .range([100, height - 100]);

  // Links
  props.links_0.forEach(link => {
    const confidence = link.confidence || 0;
    const strokeWidth = confidence * maxStrokeWidth;

    svg.append('path')
        .attr('d', d3.linkHorizontal()({
          source: [productX, height/2],
          target: [offX, offY(link.target)]
        }))
        .attr('fill', 'none')
        .attr('stroke', '#a0aec0')
        .attr('stroke-width', strokeWidth);

    addTextWithBackground(
        (productX + offX) / 2,
        (height/2 + offY(link.target)) / 2 - 10,
        confidence.toFixed(2)
    );
  });

  props.links_1.forEach(link => {
    const sourceCategory = props.off_categories.find(c => c.id === link.source);
    const isProxy = sourceCategory?.agribalyse_proxy_food_code && !sourceCategory?.agribalyse_food_code;
    const similarity = parseFloat(link.similarity || 1);

    svg.append('path')
        .attr('d', d3.linkHorizontal()({
          source: [offX, offY(link.source)],
          target: [agribalyseX, agribalyseY(link.target)]
        }))
        .attr('fill', 'none')
        .attr('stroke', '#a0aec0')
        .attr('stroke-width', isProxy ? 1 : 3)
        .attr('stroke-dasharray', isProxy ? '5,5' : '0');

    addTextWithBackground(
        (offX + agribalyseX) / 2,
        (offY(link.source) + agribalyseY(link.target)) / 2 - 10,
        typeof similarity === 'number' ? similarity.toFixed(2) : '0.00'
    );
  });

  // Basic nodes (product and OFF categories)
  [
    { x: productX, y: height/2, r: 10, fill: '#4299e1', text: props.barcode },
    ...props.off_categories.map(cat => ({
      x: offX,
      y: offY(cat.id),
      r: 8,
      fill: '#48bb78',
      text: cat.name
    }))
  ].forEach(node => {
    svg.append('circle')
        .attr('cx', node.x)
        .attr('cy', node.y)
        .attr('r', node.r)
        .attr('fill', node.fill);

    svg.append('text')
        .attr('x', node.x + 15)
        .attr('y', node.y)
        .attr('dominant-baseline', 'middle')
        .text(node.text);
  });

  // Agribalyse nodes with ecoscore
  props.agribalyse_categories.forEach(cat => {
    const baseX = agribalyseX;
    const baseY = agribalyseY(cat.id);

    // Main category circle
    svg.append('circle')
        .attr('cx', baseX)
        .attr('cy', baseY)
        .attr('r', 8)
        .attr('fill', '#ed64a6');

    // Category text with wrapping
    addWrappedText(
        baseX + 15,
        baseY,
        cat.name,
        250
    );

    // Ecoscore circle and text (if exists)
    if (cat.ecoscore) {
      const ecoscoreX = baseX + 300;

      // Line connecting category to ecoscore
      svg.append('path')
          .attr('d', `M${baseX + 10},${baseY} L${ecoscoreX - 10},${baseY}`)
          .attr('stroke', '#a0aec0')
          .attr('stroke-width', 1)
          .attr('fill', 'none');

      // Ecoscore circle
      svg.append('circle')
          .attr('cx', ecoscoreX)
          .attr('cy', baseY)
          .attr('r', 15)
          .attr('fill', '#ffd700');

      // Ecoscore text
      svg.append('text')
          .attr('x', ecoscoreX)
          .attr('y', baseY)
          .attr('text-anchor', 'middle')
          .attr('dominant-baseline', 'middle')
          .attr('font-size', '12px')
          .attr('font-weight', 'bold')
          .text(cat.ecoscore);
    }
  });

  // Legend
  const legend = svg.append('g')
      .attr('transform', `translate(${width - 300}, 10)`);

  [
    { color: '#4299e1', text: 'Produit (code-barres)' },
    { color: '#48bb78', text: 'Catégorie Open Food Facts' },
    { color: '#ed64a6', text: 'Catégorie Agribalyse' },
    { color: '#ffd700', text: 'Ecoscore simplifié (PEF)' }
  ].forEach((item, i) => {
    legend.append('circle')
        .attr('cx', 0)
        .attr('cy', i * 25)
        .attr('r', 6)
        .attr('fill', item.color);

    legend.append('text')
        .attr('x', 15)
        .attr('y', i * 25)
        .attr('dy', '0.3em')
        .text(item.text);
  });
});
</script>

<template>
  <div class="min-h-screen bg-gray-100 p-6">
    <div class="flex flex-row items-center justify-center gap-4 w-full mb-12">
      <img src="/logo-off.svg" class="h-[75px] w-[75px] object-contain"/>
      <img src="/logo-agribalyse.png" class="h-[75px] w-[75px] object-contain"/>
    </div>
    <div class="bg-white rounded-lg shadow-lg p-6">
      <svg ref="svgContainer"></svg>
    </div>
  </div>
</template>